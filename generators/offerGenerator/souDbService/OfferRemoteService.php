<?php

header('Content-Type: application/json; charset=utf-8');

include_once "include/DB.php";
include_once "include/VariaTools.php";

$variaTools = new VariaTools();
$action =  $variaTools->readFromRequestGetPost("action", "");

switch ($action) {
    case "generateNewArticleNumber" :
        $json = (new OfferRemoteService())->createNewArticleNumberJsonResponse();
        //sleep(3); // TODO remove (throttling for testing purpose)
        echo $json;
        break;
    default:
        break;
}


class OfferRemoteService {

    function getYearYY() {
        return date("y");
    }

    public function createNewArticleNumberJsonResponse() {
        return json_encode($this->generateNewArticleNumber(), JSON_UNESCAPED_UNICODE);
    }

    public function generateNewArticleNumber() {
        $sql = " update BKey           \n" .
            " set lfdNr = lfdNr + 1    \n" .
            " output inserted.lfdNr    \n" .
            " where 1 = 1              \n" .
            " and KeyName = :KeyName   \n" .
            " and Org_Nr = :OrgNr      \n" .
            " and BereichID = :Bereich \n" .
            " and Maske = :Maske       \n";

        $dbTool = new DBTool();

        $conn = $dbTool->OpenConnection();
        $ret["ok"] = false;
        $ret["value"] = -1;
        $ret["msg"] = "";

        $maske = "A" . $this->getYearYY() . "0000";

        try {
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
                $ret["value"] = "A" . $this->getYearYY() . str_pad($nr, 4, '0', STR_PAD_LEFT);;
            } else {
                $ret["msg"] = "value not found in DB!";
            }
            $conn->commit();
            $conn = null;
            return $ret;
        } catch (PDOException $e) {
            $ret["msg"] = $e->getMessage();
            $conn->rollBack();
            $conn = null;
            return $ret;
        }
    }
}
?>