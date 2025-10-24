<?php

namespace businessGenerator\souDbService;

use businessGenerator\include\VariaTools;
use businessGenerator\souDbService\AbstractBusinessObjectRemoteService;

header('Content-Type: application/json; charset=utf-8');

include_once "include/DB.php";
include_once "include/VariaTools.php";
require_once "AbstractBusinessObjectRemoteService.php";

class ArtikelRemoteService extends AbstractBusinessObjectRemoteService
{

    public function findAllArtikel()
    {

        $sql = "select Nr, Bezeichnung, KalkPreis, Einheit from Artikel order by Nr";

        return $this->dbTool->runQueryList($sql, function ($row) {
            $article = [];
            $article["Nr"] = $row["Nr"];
            $article["Bezeichnung"] = $row["Bezeichnung"];
            $article["KalkPreis"] = $row["KalkPreis"];
            $article["Einheit"] = $row["Einheit"];
            return $article;
        }, []);

    }
}

$variaTools = new VariaTools();
$ArtikelRemoteService = new ArtikelRemoteService();

$action = $variaTools->readFromRequestGetPost("action", "");

switch ($action) {
    case "findAllProducts" :
        $json = $ArtikelRemoteService->convertJson($ArtikelRemoteService->findAllArtikel());
        //sleep(3); // TODO remove (throttling for testing purpose)
        echo($json);
        break;
    default:
        break;
}


