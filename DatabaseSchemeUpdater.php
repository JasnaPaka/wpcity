<?php

class DatabaseSchemeUpdater {

    private $wpdb;
    private $tablePrefix = "";
    private $tableName = "dbversion";
    
    public function __construct($tablePrefix = "") {
        global $wpdb;
        
        $this->tablePrefix = $tablePrefix;
        $this->wpdb = $wpdb;
        
        $this->update();
    }
    
    private function update() {
        if (!$this->getIsDbSchemaTableExists()) {
            $this->createDbVersionSchemeTable();
            $this->createScheme();
        }
        
        $this->updateScheme();
    }
    
    private function getIsDbSchemaTableExists() {
        $sql = "SELECT 1 FROM ".$this->getTableName()." LIMIT 1";
        return $this->wpdb->query($sql);
    }
    
    private function createDbVersionSchemeTable() {
        $sql = "CREATE TABLE IF NOT EXISTS `".$this->getTableName()."` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `version` int(11) NOT NULL,
                PRIMARY KEY (id)
                )";
        
        if (!$this->wpdb->query($sql)) {
            die ("WPCity: Db version scheme was not created.");
        }
       
        if (!$this->wpdb->insert($this->getTableName(), array ('version' => 0), array ("%d"))) {
            die ("WPCity: Db version scheme was not created correctly.");
        }
    }
    
    private function createScheme() {
        $filename = $this->getSQLScriptsDirectory()."scheme.sql";
        $this->executeScript($filename);
    }
    
    private function executeScript($filename) {
        $handle = fopen($filename, "r");
        if ($handle) {
            $sql = "";
            while (($line = fgets($handle)) !== false) {
                $line = trim($line);
                
                $lastChar = mb_substr($line, mb_strlen($line) - 1, 1);
                
                $sql .= " ".$line;
                if ($lastChar == ";") {
                    $sql = $this->replacePrefix($sql);
                    if (!$this->wpdb->query($sql)) {
                        die("WPCity: Database scheme was not created. Script: ".$filename);
                    }
                    $sql = "";
                }
            }
            fclose($handle);
        } else {
            die("WPCity: Cannot load scheme.sql.");
        }
    }
    
    private function updateScheme() {
        $revision = $this->getDbRevision();
        $revision++;
        
        $filename = $this->getSQLScriptsDirectory().$revision.".sql";
        
        while (file_exists($filename)) {
            $this->executeScript($filename);
            $this->setDbRevision($revision);
            
            $revision++;
            $filename = $this->getSQLScriptsDirectory().$revision.".sql";
        }
    }
    
    private function getDbRevision() {
        $sql = "SELECT * FROM ".$this->getTableName()." LIMIT 1";
        $rows = $this->wpdb->get_results($sql);
        
        if (!$rows) {
            die("WPCity: Cannot get DB revision.");
        }
        
        $row = $rows[0];
        
        return $row->version;
    }
    
    private function setDbRevision($revision) {
        $values = array ("version" => $revision);
	$types = array ('%d');
        
        return $this->wpdb->update($this->getTableName(), $values, array("id" => 1), $types);
    }
    
    private function replacePrefix($sql) {
        return str_replace("{{PREFIX}}", $this->tablePrefix, $sql);
    }
    
    private function getTableName() {
        return $this->tablePrefix.$this->tableName;
    }
    
    private function getSQLScriptsDirectory() {
        return dirname(__FILE__)."/sql/";
    }

}
