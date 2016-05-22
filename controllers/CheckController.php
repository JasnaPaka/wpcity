<?php
$ROOT = plugin_dir_path( __FILE__ )."../";

include_once $ROOT."fw/JPMessages.php";
include_once $ROOT."fw/JPController.php";

include_once $ROOT."db/CategoryDb.php";
include_once $ROOT."db/ObjectDb.php"; 

/**
 * Akce na kontrolu údajů u děl
 */
class CheckController extends JPController {
    
    protected $db;
    private $dbObjects;

    function __construct() {
        $this->db = new CategoryDb();
        $this->dbObjects = new ObjectDb();
    }
    
    public function getStringId() {
        return "check";
    }
    
    private function getCategoryId() {
        return (int) filter_input (INPUT_GET, "category", FILTER_SANITIZE_STRING);
    }
    
    public function getCategory() {
        return $this->db->getById($this->getCategoryId());
    }
    
    public function getCountObjectsNoAccessibility() {
        return $this->dbObjects->getCountObjectsInCategoryNoAccessibility($this->getCategoryId());
    }
    
    public function getObjectsNoAccessibility() {
        return $this->dbObjects->getObjectsInCategoryNoAccessibility($this->getCategoryId());
    }
    
}

