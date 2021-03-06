<?php

class Object2AuthorDb extends JPDb {
	
    function __construct() {
        parent::__construct();

        $this->tableName = $this->dbPrefix."objekt2autor";
    }

    public function getDefaultOrder() {
        return "id";
    }

    public function deleteOldRelationsForObject($idObject) {
        global $wpdb;

        return $wpdb->update($this->tableName, array ("deleted" => 1), array("objekt" => $idObject), array ('%d'));
    }
	
}
	