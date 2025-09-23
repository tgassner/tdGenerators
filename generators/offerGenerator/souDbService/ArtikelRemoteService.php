<?php

header('Content-Type: application/json; charset=utf-8');

include_once "include/DB.php";
include_once "include/VariaTools.php";

$variaTools = new VariaTools();

$action =  $variaTools->readFromRequestGetPost("action", "");

switch ($action) {
    case "findAllProducts" :
        $json = (new ArtikelRemoteService())->findAllArtikelJsonResponse();
        //sleep(3); // TODO remove (throttling for testing purpose)
        echo($json);
        break;
    default:
        break;
}


class ArtikelRemoteService {

    public function findAllArtikelJsonResponse() {
        return json_encode($this->findAllArtikel(), JSON_UNESCAPED_UNICODE);
    }

    public function findAllArtikel()
    {
        $sql =  "select Nr, Bezeichnung, KalkPreis, Einheit from Artikel order by Nr";

        $dbTool = new DBTool();

        $conn = $dbTool->OpenConnection();
        $ret = [];
        $ret["ok"] = false;
        $ret["value"] = [];
        $ret["msg"] = "";

        try
        {
            $stmt = $conn->prepare($sql);
            $stmt->execute();

            $count = 0;

            while ($myrow = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if ($count <= 0) {
                    $ret["ok"] = true;
                }
                $article = [];
                $article["Nr"] = $myrow["Nr"];
                $article["Bezeichnung"] = $myrow["Bezeichnung"];
                $article["KalkPreis"] = $myrow["KalkPreis"];
                $article["Einheit"] = $myrow["Einheit"];
                $ret["value"][] = $article;
                $count++;
            }
            $conn = null;
            return $ret;
        }
        catch (PDOException $e)
        {
            $ret["msg"] = $e->getMessage();
            $conn = null;
            return $ret;
        }
    }
}




