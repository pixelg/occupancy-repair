<?php

namespace OTE\Utils;

class MySqlHelper implements IDBHelper
{
    public $mysqli;
    // public $connection;
    public $dbSettings;
    
    public function __construct($dbSettings)
    {
        $this->dbSettings = $dbSettings;
    }
    
    public function connect()
    {
        $this->mysqli = new \mysqli(
            $this->dbSettings['host'], 
            $this->dbSettings['user'], 
            $this->dbSettings['password'], 
            $this->dbSettings['db_name']
        );
        
        if (mysqli_connect_errno()){
            printf("Connect failed: %s\n", mysqli_connect_error());
            exit();
        }
        
        $this->mysqli->set_charset("utf8");
        
        // $this->connection = $this->mysqli->real_connect(
        //     $this->dbSettings['host'], 
        //     $this->dbSettings['user'], 
        //     $this->dbSettings['password'], 
        //     $this->dbSettings['db_name']
        // );
        
        // if (!$this->connection){
        //     die('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
        // }

        // echo "Connected to: " . $this->mysqli->host_info . "\n\n";
    }
    
    public function runQuery($sql)
    {
        $queryResult = $this->mysqli->query($sql);
        
        if (!$queryResult) {
            $message  = 'Invalid query: ' . $this->mysqli->error . "\n";
            $message .= 'Whole query: ' . $sql;
            die($message);
        }
        
        return $queryResult;
    }

    public function populateArray(&$queryResult, $field = null)
    {
        $results = [];
        while($row = $queryResult->fetch_assoc()){
            //echo $row['id'] . "\n";
            $results[] = is_null($field) ? $row : $row[$field];
        }
        
        $queryResult->free();
        return $results;
    }
    
    public function populateObject($queryResult, $className)
    {
        $results = [];
        while($obj = $queryResult->fetch_object($className)){
            $results[] = $obj;
        }
        
        $queryResult->free();
        return $results;
    }
    
    public function close()
    {
        $this->mysqli->close();
    }
}