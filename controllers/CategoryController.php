<?php
$ROOT = plugin_dir_path(__FILE__) . "../";

include_once $ROOT . "fw/JPMessages.php";
include_once $ROOT . "fw/JPController.php";

include_once $ROOT . "db/CategoryDb.php";
include_once $ROOT . "db/ObjectDb.php";

/**
 * Správa kategorií objektů
 */
class CategoryController extends JPController
{

	protected $db;
	private $dbObjects;

	function __construct()
	{
		$this->db = new CategoryDb();
		$this->dbObjects = new ObjectDb();
	}

	private function validate($row)
	{

		// název
		if (strlen($row->nazev) < 3 || strlen($row->nazev) > 250) {
			array_push($this->messages, new JPErrorMessage("Název kategorie musí mít min. 3 a nejvíce 250 znaků."));
		}

		// kategorie
		if (strlen($row->url) < 3 || strlen($row->url) > 250) {
			array_push($this->messages, new JPErrorMessage("URL kategorie musí mít min. 3 a nejvíce 250 znaků."));
		} else if ($this->db->getByUrl($row->url) != null &&
			($this->getObjectFromUrl() == null || $this->getObjectFromUrl()->id !== $this->db->getByUrl($row->url)->id)
		) {
			array_push($this->messages, new JPErrorMessage("URL kategorie již existuje."));
		} else if (!preg_match("/^[a-z\d-]+$/", $row->url)) {
			array_push($this->messages, new JPErrorMessage("URL smí obsahovat pouze znaky abecedy (malé), čísla a pomlčku."));
		}

		// ikona
		if (strlen($row->ikona) < 3 || strlen($row->ikona) > 250) {
			array_push($this->messages, new JPErrorMessage("Ikona musí mít min. 3 a nejvíce 250 znaků."));
		}

		if (!preg_match("%^((http?://)|(www\.))([a-z0-9-].?)+(:[0-9]+)?(/.*)?$%i", $row->ikona)) {
			array_push($this->messages, new JPErrorMessage("URL ikony není platnou adresou."));
		}

		// barva
		if (strlen($row->barva) < 3 || strlen($row->barva) > 250) {
			array_push($this->messages, new JPErrorMessage("Barva musí mít min. 3 a nejvíce 250 znaků."));
		}

		// přiblížení
		if ($row->zoom != null && ($row->zoom < 1 || $row->zoom > 18)) {
			array_push($this->messages, new JPErrorMessage("Zobrazitelné přiblížení musí být v rozsahu hodnot 1 - 18 nebo neuvedeno."));
		}

		return count($this->messages) === 0;
	}

	public function add()
	{
		$row = $this->getFormValues();

		$result = $this->validate($row);
		if ($result) {
			$result = $this->db->create($row);
			if (!$result) {
				array_push($this->messages, new JPErrorMessage("Nepodařilo se uložit novou kategorii."));
			} else {
				$idObject = $this->db->getLastId();
				array_push($this->messages, new JPInfoMessage('Kategorie byla úspěšně přidána. 
                        <a href="' . $this->getUrl(JPController::URL_LIST) . '">Zobrazit seznam</a>'));
				return new stdClass();
			}
		}

		return $row;
	}

	public function update()
	{
		$row = $this->getFormValues();
		if ($row == null) {
			return null;
		}

		$result = $this->validate($row);
		if ($result) {
			$result = $this->db->update($row, $this->getObjectFromUrl()->id);
			if (!$result) {
				array_push($this->messages, new JPErrorMessage("Kategorii se nepodařilo aktualizovat."));
			} else {
				array_push($this->messages, new JPInfoMessage('Kategorie byla úspěšně aktualizována. 
                        <a href="' . $this->getUrl(JPController::URL_LIST) . '">Zobrazit seznam</a>'));
			}
		}

		return $row;
	}

	public function getCanDelete()
	{
		$id = $this->getObjectId();
		if ($id == null) {
			return false;
		}

		return $this->dbObjects->getCountObjectsInCategory($id) == 0;
	}

	public function delete()
	{
		$row = $this->getFormValues();
		if ($row == null) {
			return null;
		}

		if (!$this->getCanDelete()) {
			array_push($this->messages, new JPErrorMessage("Kategorii nelze smazat. Pravděpodobně není prázdná."));
			return $row;
		}

		$id = $this->getObjectId();
		if ($id == null) {
			return null;
		}

		$result = $this->db->delete($id);
		if (!$result) {
			array_push($this->messages, new JPErrorMessage("Kategorii se nepodařilo smazat."));
		} else {
			array_push($this->messages, new JPInfoMessage("Kategorie byla úspěšně smazána."));
		}
	}

	public function getCountObjectsInCategory($idCategory)
	{
		return $this->db->getCountObjectsInCategory($idCategory);
	}

	public function getObjectsInCategory($idCategory)
	{
		return $this->dbObjects->getListByCategory($idCategory);
	}

	private function getFormValues()
	{
		$row = new stdClass();
		$row->nazev = filter_input(INPUT_POST, "nazev", FILTER_SANITIZE_STRING);
		$row->url = filter_input(INPUT_POST, "url", FILTER_SANITIZE_STRING);
		$row->ikona = filter_input(INPUT_POST, "ikona", FILTER_SANITIZE_STRING);
		$row->barva = filter_input(INPUT_POST, "barva", FILTER_SANITIZE_STRING);
		$row->popis = filter_input(INPUT_POST, "popis", FILTER_SANITIZE_STRING);

		$row->checked = filter_input(INPUT_POST, "checked", FILTER_SANITIZE_STRING);
		$row->checked = ($row->checked === "on" ? 1 : 0);

		$row->poradi = (int)filter_input(INPUT_POST, "poradi", FILTER_SANITIZE_STRING);
		if (!isset($row->poradi)) {
			$row->poradi = 0;
		}

		$row->zoom = (int)filter_input(INPUT_POST, "zoom", FILTER_SANITIZE_STRING);
		if (strlen($row->zoom) === 0) {
			$row->zoom = 1;
		}

		return $row;
	}

	public function initDefaults()
	{
		$row = new stdClass();
		$row->checked = true;
		$row->poradi = 0;
		return $row;
	}

	public function getStringId()
	{
		return "category";
	}

	public function getAllCategories() {
		return $this->db->getAll();
	}

}