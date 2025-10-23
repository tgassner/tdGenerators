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

    <style>

        @media print {
            #headerMain,
            .longTextHtmlClass {
                display: none !important;
            }

            #printInfo {
                display: flex !important;
                page-break-after: avoid !important;
                page-break-before: avoid !important;
            }
        }

        .oneFullOrderContainerClass {
            display: flex;
            border: #4F4F4F 1px solid;
            border-radius: 10px;
            flex-direction: column;
            margin: 10px;
            padding: 5px;
        }

        .allOrderContainerClass,
        .oneFullOrderContainerClass {
            page-break-after: avoid !important;
            page-break-before: avoid !important;
        }

        .allOrderContainerClass {
            display: flex;
            flex-direction: column;
            padding: 10px;
        }

        .posContainerClass {
            display: flex;
            border: #4F4F4F 1px solid;
            margin: 2px;
            padding: 2px;
        }

        .orderElementClass {
            margin: 3px;
            display: flex;
        }

        .posElementClass {
            margin: 3px;
            display: flex;
        }

        .orderDataContainerClass {
            display: flex;
            flex-direction: row;
        }

        .kwBtnClass {
            margin: 4px;
            padding: 3px;
            border-radius: 3px;
            display: flex;
            border: #4F4F4F 1px solid;
            box-shadow: #101010 1px 1px 1px;
            cursor: pointer;
        }

    </style>

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

                    let mitarbeiterSelect = document.getElementById("mitarbeiterSelect");
                    if (mitarbeiterSelect) {
                        allActiveUserData.forEach(function (userData) {
                            let mitarbeiterSelectOption = document.createElement('option');
                            mitarbeiterSelectOption.value = userData.Nr;
                            mitarbeiterSelectOption.innerHTML = userData.Vorname + " " + userData.Name;
                            mitarbeiterSelect.appendChild(mitarbeiterSelectOption);
                        })
                    }
                })
                .catch(function(e) {
                    alert(e);
                }).finally(function () {
            });

            const urlParams = new URLSearchParams(window.location.search);
            let mitarbeiterNr = urlParams.get("MitarbeiterNr");
            if (mitarbeiterNr) {
                fetchOpenAuftraegeByMitarbeiter(mitarbeiterNr);
            }
        })

        function presentOrderData(kwParam = null) {
            let mitarbeiterAuswahlDiv = document.getElementById("mitarbeiterAuswahlDiv")
            if (mitarbeiterAuswahlDiv) {
                mitarbeiterAuswahlDiv.remove();
            }

            const urlParams = new URLSearchParams(window.location.search);
            let mitarbeiterNr = urlParams.get("MitarbeiterNr");
            let printInfo = document.getElementById("printInfo");
            let currentDate = new Date();
            let printDate = currentDate.getDate() + "-" + (currentDate.getMonth() + 1) + "-" + currentDate.getFullYear();
            printInfo.innerHTML = mitarbeiterNr + "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" + (kwParam ? ("KW-" + kwParam) : "Alle offenen Aufträge") + "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Ausdruck am: " + printDate;

            let allOrderContainer = document.getElementById("allOrderContainer")
            allOrderContainer.innerHTML = "";

            ordersByUser.forEach(function (orderData) {
                let liefertermin = orderData["Liefertermin"]
                let kw = getWeek(liefertermin);

                if (kwParam && kw && kwParam !== kw) {
                    return;
                }

                let orderContainer = document.createElement('div');
                allOrderContainer.appendChild(orderContainer);
                orderContainer.classList.add('oneFullOrderContainerClass');

                let orderDataContainer = document.createElement('div');
                orderDataContainer.classList.add('orderDataContainerClass');
                orderContainer.appendChild(orderDataContainer);

                let nrDiv = document.createElement('div');
                nrDiv.classList.add('orderElementClass');
                nrDiv.innerHTML = orderData["Nr"];
                orderDataContainer.appendChild(nrDiv);

                let lieferterminDiv = document.createElement('div');
                lieferterminDiv.classList.add('orderElementClass');
                lieferterminDiv.innerHTML = orderData["Liefertermin"];
                orderDataContainer.appendChild(lieferterminDiv);

                let kalenderwocheDiv = document.createElement('div');
                kalenderwocheDiv.classList.add('orderElementClass');
                kalenderwocheDiv.innerHTML = "KW " + kw;
                orderDataContainer.appendChild(kalenderwocheDiv);

                let firmaDiv = document.createElement('div');
                firmaDiv.classList.add('orderElementClass');
                firmaDiv.innerHTML = orderData["Firma1_aa"] + " " + orderData["Firma2_aa"]  + " " + orderData["Firma3_aa"];
                orderDataContainer.appendChild(firmaDiv);


                //$order["Gpartner_Nr"] = $row["Gpartner_Nr"];
                //$order["BestellNr"] = $row["BestellNr"];
                //$order["BestellDatum"] = $row["BestellDatum"];
                //$order["Versandart"] = $row["Versandart"];
                //$order["Liefertermin"] = $row["Liefertermin"];
                //$order["Versandtermin"] = $row["Versandtermin"];
                //$order["Nettobetrag"] = $row["Nettobetrag"];
                //$order["Bruttobetrag"] = $row["Bruttobetrag"];
                //$order["LiefBedText"] = $row["LiefBedText"];
                //$order["Firma1_aa"] = $row["Firma1_aa"];
                //$order["Firma2_aa"] = $row["Firma2_aa"];
                //$order["Firma3_aa"] = $row["Firma3_aa"];
                //$order["Strasse_aa"] = $row["Strasse_aa"];
                //$order["LKZ_aa"] = $row["LKZ_aa"];
                //$order["PLZ_aa"] = $row["PLZ_aa"];
                //$order["Ort_aa"] = $row["Ort_aa"];
                //$order["Anschrift_aa"] = $row["Anschrift_aa"];
                //$order["ApartnerName_aa"] = $row["ApartnerName_aa"];
                //$order["Email_aa"] = $row["Email_aa"];
                //$order["Telefon_aa"] = $row["Telefon_aa"];
                //$order["Firma1_ala"] = $row["Firma1_ala"];
                //$order["Firma2_ala"] = $row["Firma2_ala"];
                //$order["Firma3_ala"] = $row["Firma3_ala"];
                //$order["Strasse_ala"] = $row["Strasse_ala"];
                //$order["LKZ_ala"] = $row["LKZ_ala"];
                //$order["PLZ_ala"] = $row["PLZ_ala"];
                //$order["Ort_ala"] = $row["Ort_ala"];
                //$order["Anschrift_ala"] = $row["Anschrift_ala"];
                //$order["ApartnerName_ala"] = $row["ApartnerName_ala"];
                //$order["Email_ala"] = $row["Email_ala"];
                //$order["Telefon_ala"] = $row["Telefon_ala"];
                //$order["Firma1_ara"] = $row["Firma1_ara"];
                //$order["Firma2_ara"] = $row["Firma2_ara"];
                //$order["Firma3_ara"] = $row["Firma3_ara"];
                //$order["Strasse_ara"] = $row["Strasse_ara"];
                //$order["LKZ_ara"] = $row["LKZ_ara"];
                //$order["PLZ_ara"] = $row["PLZ_ara"];
                //$order["Ort_ara"] = $row["Ort_ara"];
                //$order["Anschrift_ara"] = $row["Anschrift_ara"];
                //$order["ApartnerName_ara"] = $row["ApartnerName_ara"];
                //$order["Email_ara"] = $row["Email_ara"];
                //$order["Telefon_ara"] = $row["Telefon_ara"];


                //console.log(orderData);

                let posList = orderData["pos"];
                if (posList && Array.isArray(posList)) {
                    posList.forEach(function (pos) {
                        let posContainer = document.createElement('div');
                        posContainer.classList.add('posContainerClass');

                        let posNrDiv = document.createElement('div');
                        posNrDiv.classList.add('posElementClass');
                        posNrDiv.innerHTML = pos["PosNr"];
                        posContainer.appendChild(posNrDiv);

                        let bezeichnungDiv = document.createElement('div');
                        bezeichnungDiv.classList.add('posElementClass');
                        bezeichnungDiv.innerHTML = pos["Bezeichnung"];
                        posContainer.appendChild(bezeichnungDiv);

                        let artikelNrDiv = document.createElement('div');
                        artikelNrDiv.classList.add('posElementClass');
                        artikelNrDiv.innerHTML = pos["ArtikelNr"];
                        posContainer.appendChild(artikelNrDiv);

                        let mengeDiv = document.createElement('div');
                        mengeDiv.classList.add('posElementClass');
                        mengeDiv.innerHTML = pos["Menge"];
                        posContainer.appendChild(mengeDiv);

                        let einheitDiv = document.createElement('div');
                        einheitDiv.classList.add('posElementClass');
                        einheitDiv.innerHTML = pos["Einheit"];
                        posContainer.appendChild(einheitDiv);


                        // $order["Baustein"] = $row["Baustein"];
                        // $order["Preis"] = $row["Preis"];
                        // $order["PreisEinheit"] = $row["PreisEinheit"];
                        // $order["Gesamtpreis"] = $row["Gesamtpreis"];
                        // $order["Langtext"] = $row["Langtext"];

                        orderContainer.appendChild(posContainer);

                        let langTextHtmlDiv = document.createElement('div');
                        langTextHtmlDiv.classList.add('posElementClass', 'longTextHtmlClass');
                        langTextHtmlDiv.innerHTML = pos["LangtextHtml"];
                        orderContainer.appendChild(langTextHtmlDiv);
                    })
                }
            })

            let sortedKWs = Array.from(kws).sort();

            let kwArea = document.getElementById("kwArea");
            if (kwArea) {
                kwArea.innerHTML = "";

                let kwButton = document.createElement('div');
                kwButton.innerHTML = "ALLE"
                kwButton.classList.add('kwBtnClass');
                kwButton.addEventListener('click', () => filterKW(null));
                kwArea.appendChild(kwButton);

                sortedKWs.forEach((kw) => {
                    let kwButton = document.createElement('div');
                    kwButton.innerHTML = "KW-" + kw
                    kwButton.classList.add('kwBtnClass');
                    kwButton.addEventListener('click', () => filterKW(kw));
                    kwArea.appendChild(kwButton);
                })
            }
        }

        function filterKW(kw) {
            presentOrderData(kw);
        }

        function handleSearchParamBrowserUrl(mitarbeiterNr) {
            if (history.pushState) {
                let newurl = window.location.protocol + "//" + window.location.host + window.location.pathname;

                let search = window.location.search;
                if (search.startsWith("?")) {
                    search = search.substring(1);
                }

                let newSearch = "?";
                let paramArr = search.split("&");
                let mitarbeiterNrFound = false;
                paramArr.forEach(function (paramKeyValue) {
                    if (paramKeyValue) {
                        let keyValArr = paramKeyValue.split("=");
                        if (keyValArr.length >= 2) {
                            let key = keyValArr[0];
                            let val = keyValArr[1];
                            if (key && val) {
                                if (key.toLowerCase() === "MitarbeiterNr".toLowerCase()) {
                                    mitarbeiterNrFound = true;
                                    key = "MitarbeiterNr";
                                    val = mitarbeiterNr;
                                }
                                newSearch += key + "=" + val + "&";
                            }
                        }
                    }
                })
                if (!mitarbeiterNrFound) {
                    newSearch += "MitarbeiterNr=" + mitarbeiterNr;
                }
                newurl += newSearch;
                if (newurl.endsWith("$")) {
                    newurl.substring(0, newurl.length - 1);
                }
                window.history.pushState({path: newurl}, '', newurl);
            }
        }

        var ordersByUser = [];
        var kws = [];

        function fetchOpenAuftraegeByMitarbeiter(mitarbeiterNr) {

            if (!mitarbeiterNr) {
                mitarbeiterNr = document.getElementById("mitarbeiterSelect").value;
            }

            if (!mitarbeiterNr) {
                alert("aussuachn muast dan scho söwa herst...");
                return;
            }

            handleSearchParamBrowserUrl(mitarbeiterNr);

            fetch("OrderServiceRemoteCall.php?action=getOpenOrderByEmployee&MitarbeiterNr=" + mitarbeiterNr)
                .then(res => res.json())
                .then(function (res) {
                    let ok = res.ok;
                    let openOrdersByUser = res.value;
                    let msg = res.msg;

                    if (!ok) {
                        alert(msg);
                        return;
                    }

                    ordersByUser = openOrdersByUser;

                    kws = new Set();
                    ordersByUser.forEach(function (orderData) {
                        let liefertermin = orderData["Liefertermin"]
                        let kw = getWeek(liefertermin);
                        kws.add(kw);
                    });

                    presentOrderData();
                })
                .catch(function(e) {
                    alert(e);
                }).finally(function () {
            });
        }

        /**
         * thx to https://itbalance.de/javascript-und-die-kalenderwoche/
         * @param d
         * @returns {number|number|*}
         */
        function getWeek(d){

            if (!d) {
                return 0;
            }

            let date;
            if (d instanceof Date) {
                date = new Date(d.getTime());
            } else {
                // deliver JSON Date
                if (d.includes(" ")) {
                    d = d.substring(0, d.indexOf(' '));
                }
                const x = d.split('-');
                date = new Date(x[0], x[1] - 1, x[2], 11); // 11 = independent of time zone date switch
            }
            // january, 4th is always in week 1.
            const week1 = new Date(date.getFullYear(), 0, 4, 11, 0, 0, 0);
            const week1Monday = new Date(week1.getTime());
            // get the monday of the first week
            week1Monday.setDate(week1Monday.getDate() - (week1Monday.getDay() || 7) + 1);

            // check, if cal week is equal to last week of past year
            if (week1Monday.getTime() > date.getTime()) {
                if (week1Monday.getFullYear() === week1.getFullYear()) {
                    return getWeek(new Date(week1Monday.getFullYear() - 1, 11, 31, 11));
                }
                return getWeek(new Date(week1Monday.getFullYear(), 11, 31, 11));
            }

            const firstOfYear  = new Date(date.getFullYear(), 0, 1, 11, 0, 0, 0);
            const lastOfYear  = new Date(date.getFullYear(), 11, 31, 11, 0, 0, 0);

            // check if current year is a leap year
            const isLeap = date.getFullYear() % 4 === 0 && date.getFullYear() % 100 > 0;
            // check if current year has 53 weeks
            const has53 = !isLeap && (firstOfYear.getDay() === 4 && lastOfYear.getDay() === 4)  ||
                (isLeap && (firstOfYear.getDay() === 3 && lastOfYear.getDay() === 4 || firstOfYear.getDay() === 4 && lastOfYear.getDay() === 5));

            const dateMonday = new Date(date.getTime());
            dateMonday.setDate(dateMonday.getDate() - (dateMonday.getDay() || 7) + 1);

            // round is needed due to summer time / winter time difference ends up in non integer calculation
            const weekOffset= (dateMonday.getTime() - week1Monday.getTime()) / 1000 / 60 / 60 / 24;

            const result =  1 + Math.round(weekOffset / 7);
            if(result === 53 && !has53) {
                return 1;
            } else {
                return result;
            }
        }

    </script>

</head>
<body style="margin: 0px; ">

    <div id="printInfo" style="display: none">Döner macht schöner</div>

    <div id="headerMain" style="display: flex; flex-flow: row; background-color: #ffdc14; background: linear-gradient(135deg, rgba(255,205,0,1) 0%, rgba(255,220,20,1) 35%, rgba(255,205,0,1) 100%);padding-left: 20px; justify-content: space-between;height:100px">
        <div style="display: flex; flex-flow: column;">
            <h1 style="display: flex;margin-top: 5px;margin-bottom: 2px;font-family: Khand, Helvetia, Arial, sans-serif; font-size: 34px; font-weight: 600; color:#000000;">TD Auftrags Viewer</h1>
            <div id="headerLowerDiv" style="display: flex; margin-top: 0px;margin-bottom: 15px;">Version 0.2.5&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;23.10.2025 <span id="statusSpan" style="margin-left: 10px"> </span></div>
        </div>
        <div style="display: flex">
            <div id="kwArea" style="display: flex; flex-flow: row; margin-top: auto">
            </div>
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

    <div id="allOrderContainer" class="allOrderContainerClass"></div>

</body>
</html>