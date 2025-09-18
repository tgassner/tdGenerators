<?php

header('Content-Type: application/json; charset=utf-8');

function openConnection() {
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

function getYearYY() {
    return date("y");
}

function readData()
{
    $sql =  " update BKey              \n" .
            " set lfdNr = lfdNr + 1    \n" .
            " output inserted.lfdNr    \n" .
            " where 1 = 1              \n" .
            " and KeyName = :KeyName   \n" .
            " and Org_Nr = :OrgNr      \n" .
            " and BereichID = :Bereich \n" .
            " and Maske = :Maske       \n";

    $conn = OpenConnection();
    $ret = [];
    $ret["ok"] = false;
    $ret["value"] = -1;
    $ret["msg"] = "";

    $maske = "A" . getYearYY() . "0000";

    try
    {
        $conn->beginTransaction();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue("KeyName", "ANGEBOT", PDO::PARAM_STR);
        $stmt->bindValue("OrgNr", "_alle_", PDO::PARAM_STR);
        $stmt->bindValue("Bereich", 0, PDO::PARAM_INT);
        $stmt->bindValue("Maske", $maske, PDO::PARAM_STR);
        $stmt->execute();
        $myrow = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($myrow) {
            $ret["ok"] = true;
            $nr = $myrow["lfdNr"];
            $ret["value"] = "A" . getYearYY() . str_pad($nr, 4, '0', STR_PAD_LEFT);;
        } else {
            $ret["msg"] = "value not found in DB!";
        }
        $conn->commit();
        $conn = null;
        return $ret;
    }
    catch (PDOException $e)
    {
        $ret["msg"] = $e->getMessage();
        $conn->rollBack();
        $conn = null;
        return json_encode($ret, JSON_UNESCAPED_UNICODE);
    }
}

$ret = readData();
$json = json_encode($ret, JSON_UNESCAPED_UNICODE);
//sleep(3); // TODO remove
echo $json;

?>