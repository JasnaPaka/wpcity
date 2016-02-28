<?php

abstract class JPDb {
	
    protected $tableName;
    protected $dbPrefix;

    CONST MAX_ITEMS_ON_PAGE = 20;	

    function __construct() {
        global $wpdb;


        if (is_multisite()) {
            $this->dbPrefix = "kv_".$wpdb->blogid."_";
        } else {
            $this->dbPrefix = "kv_";	
        }

        $wpdb->show_errors();	
    }

    protected function getOrderSQL($param) {
        return $this->getDefaultOrder();
    } 

    public function getAll($order = "") {
        global $wpdb;

        return $wpdb->get_results("SELECT * FROM ".$this->tableName." WHERE deleted = 0 ORDER BY ".$this->getOrderSQL($order));	
    }

    public function getPage($page, $order = "") {
        global $wpdb;

        $page--;
        $offset = $page * JPDb::MAX_ITEMS_ON_PAGE;

        return $wpdb->get_results("SELECT * FROM ".$this->tableName." WHERE deleted = 0 ORDER BY ".$this->getOrderSQL($order)." LIMIT ".JPDb::MAX_ITEMS_ON_PAGE." OFFSET ".$offset);
    }

    public function getCount() {
        global $wpdb;

        return $wpdb->get_var ("SELECT count(*) FROM ".$this->tableName." WHERE deleted = 0");
    }

    public function getById($id) {
        global $wpdb;

        $sql = $wpdb->prepare("SELECT * FROM ".$this->tableName." WHERE id = %d AND deleted = 0", $id); 
        $rows = $wpdb->get_results ($sql);
        if (count($rows) === 0) {
                return null;
        }

        return $rows[0];
    }

    public function create($data) {
        global $wpdb;

        return $wpdb->insert($this->tableName, (array) $data);
    }

    public function update($data, $id) {
        global $wpdb;

        $data->id = $id;
        return $wpdb->replace($this->tableName, (array) $data);
    }

    public function delete($id) {
        global $wpdb;

        return $wpdb->update($this->tableName, array ("deleted" => 1), array("id" => $id), array ('%d'));
    }

    public function getLastId() {
        global $wpdb;

        return $wpdb->insert_id;
    }

    public function getWpdb() {
        global $wpdb;
        return $wpdb;
    }

    abstract public function getDefaultOrder();

}