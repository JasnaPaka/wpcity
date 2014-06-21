<?php

include_once "category-db.php";
include_once "jp\JPMessages.php"; 

class CategoryController {

	private $db;
	private $messages = array();
	
	function __construct() {
		$this->db = new CategoryDb();
	}

	public function getList() {
		return $this->db->getAll();	
	}
	
	private function validateAdd($nazev, $url, $ikona) {
		
		// název
		if (strlen($nazev) < 3 || strlen($nazev) > 250) {
			array_push($this->messages, new JPErrorMessage("Název kategorie musí mít min. 3 a nejvíce 250 znaků."));
		}
		
		// kategorie
		if (strlen($url) < 3 || strlen($url) > 250) {
			array_push($this->messages, new JPErrorMessage("URL kategorie musí mít min. 3 a nejvíce 250 znaků."));
		} else if (count($this->db->findByUrl($url)) > 0) {
			array_push($this->messages, new JPErrorMessage("URL kategorie již existuje."));
		} else if (!preg_match("/^[a-z\d-]+$/", $url)) {
			array_push($this->messages, new JPErrorMessage("URL smí obsahovat pouze znaky abecedy (malé), čísla a pomlčku."));
		}
		
		// ikona
		if (!preg_match("%^((http?://)|(www\.))([a-z0-9-].?)+(:[0-9]+)?(/.*)?$%i", $ikona)) {
			array_push($this->messages, new JPErrorMessage("URL ikony není platnou adresou."));
		}
		
		return count($this->messages) === 0; 
	}
	
	public function add() {
		$nazev = filter_input (INPUT_POST, "nazev", FILTER_SANITIZE_STRING);
		$url = filter_input (INPUT_POST, "url", FILTER_SANITIZE_STRING);
		$ikona = filter_input (INPUT_POST, "ikona", FILTER_SANITIZE_STRING);
		
		$result = $this->validateAdd($nazev, $url, $ikona);
		if ($result) {
			
			$data = array(
				"nazev" => $nazev,
				"url" => $url,
				"ikona" => $ikona
			);
			
			$result = $this->db->create($data);
			if (!$result) {
				array_push($this->messages, new JPErrorMessage("Nepodařilo se uložit novou kategorii."));
			} else {
				array_push($this->messages, new JPInfoMessage("Kategorie byla úspěšně přidána."));
				return new stdClass();
			}
		}
		
		$row = new stdClass();
		$row->nazev = $nazev;
		$row->url = $url;
		$row->ikona = $ikona;
		
		return $row;
	}
	
	public function getMessages() {
		return $this->messages;	
	}
	
	public function getErrorMessages() {
		$messages = array();
		foreach($this->messages as $message) {
			if ($message instanceof JPErrorMessage) {
				array_push($messages, $message);	
			}
		}
		
		return $messages;			
	}
	
	public function getInfoMessages() {
		$messages = array();
		foreach($this->messages as $message) {
			if ($message instanceof JPInfoMessage) {
				array_push($messages, $message);	
			}
		}
		
		return $messages;			
	}
	
	// TODO: stejne metody pro warn messages
		
}

?>