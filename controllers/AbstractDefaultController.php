<?php

$ROOT = plugin_dir_path(__FILE__) . "../";

include_once $ROOT . "fw/JPController.php";
include_once $ROOT . "fw/ImgUtils.php";

abstract class AbstractDefaultController extends JPController
{

	protected $dbPhoto;

	protected function refreshPhoto($photo)
	{
		$author = filter_input(INPUT_POST, "autor" . $photo->id, FILTER_SANITIZE_STRING);
		$description = filter_input(INPUT_POST, "popis" . $photo->id, FILTER_SANITIZE_STRING);
		$url = filter_input(INPUT_POST, "url" . $photo->id, FILTER_SANITIZE_STRING);
		$rok = filter_input(INPUT_POST, "rok" . $photo->id, FILTER_SANITIZE_STRING);

		$id = "primarni" . $photo->id;
		if (isset($_POST[$id])) {
			$primary = 1;
		} else {
			$primary = 0;
		}

		$id = "soukroma" . $photo->id;
		if (isset($_POST[$id])) {
			$soukroma = 1;
		} else {
			$soukroma = 0;
		}

		$id = "skryta" . $photo->id;
		if (isset($_POST[$id])) {
			$skryta = 1;
		} else {
			$skryta = 0;
		}


		$photo->autor = $author;
		$photo->popis = $description;
		$photo->primarni = $primary;
		$photo->soukroma = $soukroma;
		$photo->skryta = $skryta;
		$photo->url = $url;
		$photo->rok = $rok;

		return $photo;
	}

	protected function validatePhotos($photos)
	{
		// právě jedna fotka musí být hlavní (primární)
		$foundPrimary = false;
		$duplicatePrimary = false;

		foreach ($photos as $photo) {
			$id = "primarni" . $photo->id;

			if (isset($_POST[$id])) {
				if (!$foundPrimary) {
					$foundPrimary = true;
				} else {
					$duplicate = true;
				}
			}

			$author = filter_input(INPUT_POST, "autor" . $photo->id, FILTER_SANITIZE_STRING);
			if (strlen($author) > 255) {
				array_push($this->messages, new JPErrorMessage("Jméno autora nesmí být delší než 255 znaků."));
			}

			$url = filter_input(INPUT_POST, "url" . $photo->id, FILTER_SANITIZE_STRING);
			if (strlen($url) > 255) {
				array_push($this->messages, new JPErrorMessage("Adresa (URL) u fotografie nesmí být delší než 255 znaků."));
			}
		}

		if (!$foundPrimary) {
			array_push($this->messages, new JPErrorMessage("Žádná z nahraných fotografií není zvolena jako hlavní."));
		}
		if ($duplicatePrimary) {
			array_push($this->messages, new JPErrorMessage("Jako hlavní fotografie smí být zvolena pouze jediná."));
		}

		return count($this->messages) === 0;
	}


	protected function addPhotos($idObject, $idAuthor, $idCollection, $existPhotos)
	{
		global $_wp_additional_image_sizes;
		global $current_user;

		$sizes = array();
		if (!function_exists('wp_handle_upload')) {
			require_once(ABSPATH . 'wp-admin/includes/file.php');
		}

		$upload_overrides = array('test_form' => false);
		$uploadFiles = array();

		// Zpracování multiupload
		$files = $_FILES['photo'];
		foreach ($files['name'] as $key => $value) {
			if ($files['name'][$key]) {
				$file = array(
					'name' => $files['name'][$key],
					'type' => $files['type'][$key],
					'tmp_name' => $files['tmp_name'][$key],
					'error' => $files['error'][$key],
					'size' => $files['size'][$key]
				);
				array_push($uploadFiles, $file);
			}
		}

		$photos = array();
		$newPhotos = array();
		$isFirst = true;
		foreach ($uploadFiles as $uploadFile) {
			$result = wp_handle_upload($uploadFile, $upload_overrides);

			if ($result["error"] != null) {
				array_push($this->messages, new JPErrorMessage("Při nahrávání fotky '" . $uploadFile["name"] . "' nastala chyba,
                        díky které fotka nebyla nahrána. Chyba: " . $result["error"]));
			} else {
				$photos[0] = $this->getRelativePathToImg($result["file"]);

				// vše ok, máme info o obrázku, vygenerujeme náhledy
				$sizes["thumbnail"] = array(200, 200, true);
				$sizes["medium"] = array(600, 600, true);
				$sizes["large"] = array(1024, 1024, true);
				$sizes["512"] = array(512, 384, false);
				$sizes["100"] = array(100, 75, false);

				$i = 0;
				foreach ($sizes as $size) {
					$i++;
					$image = wp_get_image_editor($result["file"]);

					if (!is_wp_error($image)) {

						$imgSize = $image->get_size();
						if ($size[2]) {
							$imgSize = ImgUtils::resizeToProporcional($imgSize["width"], $imgSize["height"], $size[0], $size[1], $size[2]);
						} else {
							$imgSize["width"] = $size[0];
							$imgSize["height"] = $size[1];
						}

						$image->resize($imgSize["width"], $imgSize["height"], true);
						$filename = $image->generate_filename($size[0]);
						$output = $image->save($filename);

						// Korekce cesty k souboru pro uložení do db
						$path = $this->getRelativePathToImg($output["path"]);
						$photos[$i] = $path;
					} else {
						array_push($this->messages, new JPErrorMessage("Pro fotografii '" . $uploadFile['name'] . " se nepodařilo
                                    vygenerovat náhled: " . $size[0] . 'x' . $size[1] . " Chyba: " . $image->get_error_message()));
					}
				}

				// všechny náhledy vygenerovány?
				if (count($photos) == 6) {
					$photo = new stdClass();
					$photo->img_original = $photos[0];
					$photo->img_thumbnail = $photos[1];
					$photo->img_medium = $photos[2];
					$photo->img_large = $photos[3];
					$photo->img_512 = $photos[4];
					$photo->img_100 = $photos[5];
					$photo->objekt = $idObject;
					$photo->autor_id = $idAuthor;
					$photo->soubor = $idCollection;
					$photo->autor = $current_user->display_name;
					$photo->primarni = 0;
					$photo->datum_nahrani = date('Y-m-d');

					if ($isFirst && !$existPhotos) {
						$photo->primarni = 1;
						$isFirst = false;
					}

					$result = $this->dbPhoto->create($photo);
					if (!$result) {
						array_push($this->messages, new JPErrorMessage("Fotografii '" . $uploadFile['name'] . " se nepodařilo uložit."));
					} else {
						array_push($newPhotos, $this->dbPhoto->getById($this->dbPhoto->getLastId()));
					}
				}
			}
		}

		return $newPhotos;

	}

	protected function getRelativePathToImg($path)
	{
		$upload_dir = wp_upload_dir();
		$baseDir = $upload_dir['basedir'];
		return str_replace($baseDir, "", $path);
	}
}