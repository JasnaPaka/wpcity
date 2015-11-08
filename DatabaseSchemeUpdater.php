<?php

class DatabaseSchemeUpdater {

    private $wpdb;
    private $tablePrefix = "";
    private $tableName = "dbversion";
    
    public function __construct($tablePrefix = "") {
        global $wpdb;
        
        $this->tablePrefix = $tablePrefix;
        $this->wpdb = $wpdb;
        
        $this->updateScheme();
    }
    
    private function updateScheme() {
        if (!$this->getIsDbSchemaTableExists()) {
            $this->createDbVersionSchemeTable();
            $this->createScheme();
        }
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
            die ("Db version scheme was not created.");
        }
       
        if (!$this->wpdb->insert($this->getTableName(), array ('version' => 0), array ("%d"))) {
            die ("Db version scheme was not created correctly.");
        }
    }
    
    private function createScheme() {
        $filename = $this->getSQLScriptsDirectory()."scheme.sql";
        
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
                        die("Database scheme was not created.");
                    }
                    $sql = "";
                }
            }
            fclose($handle);
        } else {
            die("Cannot load scheme.sql.");
        }
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
