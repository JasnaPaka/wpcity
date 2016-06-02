<?php

$ROOT = plugin_dir_path( __FILE__ )."../";

include_once $ROOT."db/ObjectDb.php"; 
include_once $ROOT."db/AuthorDb.php";
include_once $ROOT."db/CollectionDb.php";

/**
 * Mapa webu.
 */
class SitemapController {
    
    private $dbObject;
    private $dbAuthor;
    private $dbCollection;
    
    function __construct() {
        $this->dbObject = new ObjectDb();
        $this->dbAuthor = new AuthorDb();
        $this->dbCollection = new CollectionDb();
    }
    
    public function getObjects() {
        return $this->dbObject->getAllPublic();
    }
    
    public function getAuthors() {
        return $this->dbAuthor->getAll();
    }
    
    public function getCollections() {
        return $this->dbCollection->getAll();
    }
}
