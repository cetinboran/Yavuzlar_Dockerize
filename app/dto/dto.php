<?php

require_once("../dto/query.php");

class DTO{
    private $dbType;
    private $connSqlite;
    private $connMysql;

    private $query;

    // Type sqlite girilirse girilen path altında db oluşturuyor.
    // Type mysql girilirse girilen bilgiler ile db ye bağlanıyor
    // İkinde de PDO kullandığım için birleştirebildim.
    function __construct($path, $type, $mysql) {
        $this->dbType = $type;

        if($type == "sqlite"){
            $this->connSqlite = new PDO("sqlite:$path");
        } else if($type == "mysql"){
            if($mysql != null){
                $username = $mysql[0];
                $password = $mysql[1];
                $db = $mysql[2];

                // $this->connMysql = new PDO("mysql:host=localhost;dbname=$db", $username, $password);
            }            
        }

        $this->connMysql = new PDO("mysql:host=dockerize-mysql-1;port=3306;dbname=yavuzlar_obs", "test", "test");
        $this->query = new Query();
    }

    function Get(){
        return $this->connMysql;
    }

    // 1 => table name, 2 => [colunm Name => types]
    // "selam", ["selamId" => "INTEGER, PRIMARY KEY", "selamName" => "TEXT", "selamPassword" => "TEXT"] => SQLITE
    // "selam", ["selamId" => "INT AUTO_INCREMENT PRIMARY KEY", "selamName" => "VARCHAR(255)", "selamPassword" => "VARCHAR(255)" => MYSQL
    function CreateTable($tableName, $columns){
        if(!is_array($columns)){
            return null;
        }

        if(count($columns) < 1){
            return null;
        }

        $tableQuery = $this->query->tableQuery($tableName, $columns);

        $dbType = $this->dbType;
        if($dbType == "sqlite"){
            $conn = $this->connSqlite;
        } else if($dbType == "mysql"){
            $conn = $this->connMysql;
        }

        $conn->exec($tableQuery);

        return $tableQuery;
    }

    // Select("users", [], []) => Normal Select
    // Select("users", ["userId"], [8]) => UserId 8 olanı döndürür. 
    function Select($tableName, $columns, $values){
        $selectQuery = $this->query->CreateQuery("select", $tableName, $columns);

        if(!is_array($values)){
            return null;
        }

        if(count($values) != count($columns)){
            return null;
        }

        $dbType = $this->dbType;
        if($dbType == "sqlite"){
            $conn = $this->connSqlite;
        } else if($dbType == "mysql"){
            $conn = $this->connMysql;
        }

        $stmt = $conn->prepare($selectQuery);

        for($i = 0; $i < count($values); $i++){
            $stmt->bindParam($columns[$i], $values[$i]);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // "users", ["username", "password"], ["yeniben", "yeniben"] => users'tablosuna istenilen değerleri ekler.
    function Insert($tableName, $columns, $values){
        $insertQuery = $this->query->CreateQuery("insert", $tableName, $columns);

        if(!is_array($values)){
            return null;
        }

        if(count($values) != count($columns)){
            return null;
        }

        $dbType = $this->dbType;
        if($dbType == "sqlite"){
            $conn = $this->connSqlite;
        } else if($dbType == "mysql"){
            $conn = $this->connMysql;
        }

        $stmt = $conn->prepare($insertQuery);

        for($i = 0; $i < count($values); $i++){
            $stmt->bindParam($columns[$i], $values[$i]);
        }

        $stmt->execute();
    }

    // "users", ["username", "password", "userId"], ["degistirdim", "degistirdim", 3] => userId = 3 olanın username ise password'unu değiştirir.
    function Update($tableName, $columns, $values){
        $updateQuery = $this->query->CreateQuery("update", $tableName, $columns);

        if(!is_array($values)){
            return null;
        }

        if(count($values) != count($columns)){
            return null;
        }

        // Bu fonksiyonda $values'ın son değeri WHERE için değer
        $dbType = $this->dbType;
        if($dbType == "sqlite"){
            $conn = $this->connSqlite;
        } else if($dbType == "mysql"){
            $conn = $this->connMysql;
        }

        $stmt = $conn->prepare($updateQuery);

        for($i = 0; $i < count($values); $i++){
            $stmt->bindParam($columns[$i], $values[$i]);
        }

        $stmt->execute();
    }

    // "users", ["userId"], [1] => userId = 1 olanı siler users tablosundan.
    function Delete($tableName, $columns, $values){
        $deleteQuery = $this->query->CreateQuery("delete", $tableName, $columns);

        if(!is_array($values)){
            return null;
        }

        if(count($values) != count($columns)){
            return null;
        }

        $dbType = $this->dbType;
        if($dbType == "sqlite"){
            $conn = $this->connSqlite;
        } else if($dbType == "mysql"){
            $conn = $this->connMysql;
        }

        $stmt = $conn->prepare($deleteQuery);

        for($i = 0; $i < count($values); $i++){
            $stmt->bindParam($columns[$i], $values[$i]);
        }

        $stmt->execute();
    }
    
}