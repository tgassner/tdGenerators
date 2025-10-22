<?php
header('Cache-Control: no-cache, no-store');
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="cache-control" content="max-age=0" />
    <meta http-equiv="cache-control" content="no-cache" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="img/favicon114_order.png" sizes="32x32" />
    <link rel="icon" href="img/favicon114_order.png" sizes="192x192" />
    <link rel="apple-touch-icon" href="img/favicon114_order.png" />
    <meta name="msapplication-TileImage" content="img/favicon114_order.png" />

    <title>TD Auftragsviewer</title>

    <script>

        document.addEventListener("DOMContentLoaded", function() {
            fetch("UserServiceRemoteCall.php?action=getActiveUsers") // Call the fetch function passing the url of the API as a parameter
                .then(res => res.json())
                .then(function (res) {
                    let ok = res.ok;
                    let allActiveUserData = res.value;
                    let msg = res.msg;

                    if (!ok) {
                        alert(msg);
                        return;
                    }

                    allActiveUserData.forEach(function (userData) {
                        let mitarbeiterSelectOption = document.createElement('option');
                        mitarbeiterSelectOption.value = userData.Nr;
                        mitarbeiterSelectOption.innerHTML = userData.Vorname + " " + userData.Name;
                        document.getElementById("mitarbeiterSelect").appendChild(mitarbeiterSelectOption);
                    })
                })
                .catch(function(e) {
                    alert(e);
                }).finally(function () {
            });
        })

        function fetchOpenAuftraegeByMitarbeiter() {

            let mitarbeiterNr = document.getElementById("mitarbeiterSelect").value;

            if (!mitarbeiterNr) {
                alert("aussuachn muast dan scho söwa herst...");
                return;
            }

            fetch("OrderServiceRemoteCall.php?action=getOpenOrderByEmployee&MitarbeiterNr=" + mitarbeiterNr) // Call the fetch function passing the url of the API as a parameter
                .then(res => res.json())
                .then(function (res) {
                    let ok = res.ok;
                    let openOrdersByUser = res.value;
                    let msg = res.msg;

                    if (!ok) {
                        alert(msg);
                        return;
                    }

                    openOrdersByUser.forEach(function (orderData) {
                        console.log(orderData);
                    })
                })
                .catch(function(e) {
                    alert(e);
                }).finally(function () {
            });
        }

    </script>

</head>
<body style="margin: 0px; ">

    <div style="display: flex; flex-flow: row; background-color: #ffdc14; background: linear-gradient(135deg, rgba(255,205,0,1) 0%, rgba(255,220,20,1) 35%, rgba(255,205,0,1) 100%);padding-left: 20px; justify-content: space-between;height:100px">
        <div style="display: flex; flex-flow: column;">
            <h1 style="display: flex;margin-top: 5px;margin-bottom: 2px;font-family: Khand, Helvetia, Arial, sans-serif; font-size: 34px; font-weight: 600; color:#000000;">TD Auftrags Viewer</h1>
            <div id="headerLowerDiv" style="display: flex; margin-top: 0px;margin-bottom: 15px;">Version 0.1.&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;22.10.2025 <span id="statusSpan" style="margin-left: 10px"> </span></div>
        </div>
        <img src="img/td_header.png" height="150px" style="; display: flex; justify-content: right; align-self: center;filter: drop-shadow(8px 8px 40px #FFB214);">
    </div>

    <div id="mitarbeiterAuswahlDiv" name="mitarbeiterAuswahlDiv">
        <label for="mitarbeiterSelect">Aufträge welchen Mitarbeiters</label>
        <select id="mitarbeiterSelect" name="mitarbeiterSelect" >
            <option value=""></option>
        </select>

        <button onclick="fetchOpenAuftraegeByMitarbeiter()">Aufträge laden</button>

    </div>


</body>
</html>