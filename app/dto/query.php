<?php

class Query{
    function CreateQuery($type, $tableName, $columns){
        switch($type){
            case "select":
                return $this->selectQuery($tableName, $columns);
            case "insert":
                return $this->insertQuery($tableName, $columns);
            case "update":
                    return $this->updateQuery($tableName, $columns);
            case "delete":
                return $this->deleteQuery($tableName, $columns);
        }

    }


    function tableQuery($tableName, $columns){
        $query = "CREATE TABLE IF NOT EXISTS $tableName (";

        foreach($columns as $column => $types){
            $query .= "$column ";

            $typeArr = explode(",", $types);
            foreach($typeArr as $type){
                $query .= $type . " ";
            }
            $query = rtrim($query, " ");
            $query .= ",";
        }

        $query = rtrim($query, ",");
        $query .= ")";

        return $query;
    }

    function selectQuery($tableName, $columns){
        $query = "SELECT * FROM $tableName";

        if(count($columns) > 0){
            $query .= " WHERE";

            foreach ($columns as $column) {
                $query .= " $column = :$column AND";
            }

            $query = rtrim($query,"AND");
        }
        
        return $query;
    }

    function insertQuery($tableName, $columns){
        if(!is_array($columns)){
            return null;
        }

        if(count($columns) <= 0){
            return null;
        }


        $query = "INSERT INTO $tableName (";

        foreach ($columns as $column) {
            $query .= "$column,";
        }
        $query = rtrim($query,",");
        $query .= ")";

        $query .= " VALUES (";
        foreach ($columns as $column) {
            $query .= ":$column,";
        }
        $query = rtrim($query,",");
        $query .= ")";

        return $query;
    }

    function updateQuery($tableName, $columns){
        if(!is_array($columns)){
            return null;
        }

        if(count($columns) <= 0){
            return null;
        }

        // Bu func'ta $columns array'inin son değer where için değer set için seğil
      
        $length = count($columns);
        $query = "UPDATE $tableName SET ";

        for($i = 0; $i < $length -1; $i++){
            $query .= "$columns[$i] = :$columns[$i], ";
        }
        $query = rtrim($query," ,");

        $query .= " WHERE " . $columns[$length-1] . " = :".$columns[$length-1];

        return $query;
    }

    function deleteQuery($tableName, $columns){
        if(!is_array($columns)){
            return null;
        }

        if(count($columns) <= 0){
            return null;
        }

      
        $query = "DELETE FROM $tableName WHERE";

        foreach ($columns as $column) {
            $query .= " $column = :$column AND";
        }

        $query = rtrim($query,"AND");

        return $query;
    }
}