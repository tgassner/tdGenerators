<?php

class DBTool {
    public function openConnection() {
        $DB_LOGIN = json_decode(file_get_contents("DB.json", true));

        $dbhost = $DB_LOGIN->dbhost;
        $dbuser = $DB_LOGIN->dbuser;
        $dbpass = $DB_LOGIN->dbpass;
        $dbname = $DB_LOGIN->dbname;

        $dbh = new PDO(
            "sqlsrv:Server=$dbhost;Database=$dbname",
            $dbuser,
            $dbpass ,
            array(//PDO::ATTR_PERSISTENT => true, // Verbindung bleibt bis scriptende gecached !
                //PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

        return $dbh;
    }
}