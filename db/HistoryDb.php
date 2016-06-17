<?php

class HistoryDb extends JPDb {

  function __construct() {
    parent::__construct();

    $this->tableName = $this->dbPrefix."historie";
  }

  public function getDefaultOrder() {
    return "id DESC";
  }

  public function update($data, $id) {
    throw new Exception("Zaznam v historii nelze aktualizovat.");
  }

  public function delete($id) {
    throw new Exception("Zaznam v historii nelze smazat.");
  }

  public function getHistoryForObject($idObject) {
    global $wpdb;

    $sql = $wpdb->prepare("SELECT * FROM ".$this->tableName." WHERE objekt = %d ORDER BY ".$this->getDefaultOrder(), $idObject);
    return $wpdb->get_results($sql);
  }
}
