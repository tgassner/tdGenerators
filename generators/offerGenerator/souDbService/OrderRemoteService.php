<?php

header('Content-Type: application/json; charset=utf-8');

include_once "include/DB.php";
include_once "include/VariaTools.php";
require_once "AbstractBusinessObjectRemoteService.php";

class OrderRemoteService extends AbstractBusinessObjectRemoteService {

    public function generateNewOrderNumber() {
        return $this->generateNewBusinessObjectNumber("K", "AUFTRAG");
    }

    public function getOpenOrderByEmployee() {
        $sql =
            " select                                                                                                                                                              \n" .
            " a.Nr, a.Gpartner_Nr, a.BestellNr, a.BestellDatum, Versandart, a.Liefertermin, a.Versandtermin, a.Nettobetrag, a.Bruttobetrag, a.LiefBedText,                        \n" .
            " aa.Firma1 as Firma1_aa,  aa.Firma2 as Firma2_aa,  aa.Firma3 as Firma3_aa,  aa.Strasse as Strasse_aa,  aa.LKZ as LKZ_aa,                                             \n" .
            " aa.PLZ as PLZ_aa,  aa.Ort as Ort_aa,  aa.Anschrift as Anschrift_aa,  aa.ApartnerName as ApartnerName_aa,  aa.Email as Email_aa,  aa.Telefon as Telefon_aa,          \n" .
            " ala.Firma1 as Firma1_ala, ala.Firma2 as Firma2_ala, ala.Firma3 as Firma3_ala, ala.Strasse as Strasse_ala, ala.LKZ as LKZ_ala,                                       \n" .
            " ala.PLZ as PLZ_ala, ala.Ort as Ort_ala, ala.Anschrift as Anschrift_ala, ala.ApartnerName as ApartnerName_ala, ala.Email as Email_ala, ala.Telefon as Telefon_ala,   \n" .
            " ara.Firma1 as Firma1_ara, ara.Firma2 as Firma2_ara, ara.Firma3 as Firma3_ara, ara.Strasse as Strasse_ara, ara.LKZ as LKZ_ara,                                       \n" .
            " ara.PLZ as PLZ_ara, ara.Ort as Ort_ara, ara.Anschrift as Anschrift_ara, ara.ApartnerName as ApartnerName_ara, ara.Email as Email_ara, ara.Telefon as Telefon_ara,   \n" .
            " ap.PosNr, ap.Baustein, ap.ArtikelNr, ap.Bezeichnung, ap.Menge, ap.Einheit, ap.Preis, ap.PreisEinheit, ap.Gesamtpreis, ap.Langtext, ap.LangtextHtml                  \n" .
            " from Auftrag a                                                                                                                                                      \n" .
            " left join AuftragAdr aa on a.AuftragAdr_ID = aa.ID                                                                                                                  \n" .
            " left join AuftragAdr ala on a.AuftragLiefAdr_ID = ala.ID                                                                                                            \n" .
            " left join AuftragAdr ara on a.AuftragRechAdr_ID = ara.ID                                                                                                            \n" .
            " left join AuftragPos ap on a.ID = ap.Auftrag_ID                                                                                                                     \n" .
            " where 1 = 1                                                                                                                                                         \n" .
            " and MitarbeiterNr = :MitarbeiterNr                                                                                                                                  \n" .
            " and AbschlFertigung = 0                                                                                                                                             \n" .
            " order by a.Liefertermin, a.Versandtermin, a.Nr, ap.PosNr                                                                                                            \n";

        $dbTool = new DBTool();
        $mitarbeiterId = $this->variaTools->readFromRequestGetPost("MitarbeiterNr", "");

        $ret = $dbTool->runQueryList($sql, function($row, $resultSet) {
            return print_r($row);
            // TODO;
        }, array('MitarbeiterNr' => $mitarbeiterId));

        return $ret;
    }
}

$variaTools = new VariaTools();
$orderRemoteService = new OrderRemoteService($variaTools);

$action =  $variaTools->readFromRequestGetPost("action", "");

switch ($action) {
    case "getOpenOrderByEmployee" :
        $json = $orderRemoteService-> $orderRemoteService->getOpenOrderByEmployeeJsonResponse();
        echo $json;
        break;
    case "generateNewOrderNumber" :
        $json = $orderRemoteService->convertJson($orderRemoteService->generateNewOrderNumber());
        //sleep(3); // TODO remove (throttling for testing purpose)
        echo $json;
        break;
    default:
        break;
}