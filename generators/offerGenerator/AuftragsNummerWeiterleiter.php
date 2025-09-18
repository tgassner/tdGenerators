<?php
header('Content-Type: application/json; charset=utf-8');
$DBServerErmittlerUrl = file_get_contents("DBServerErmittlerUrl.txt");

$opts = [
    "http" => [
        "method" => "GET",
        "header" => "Accept-language: de-AT, de;q=0.9, en;q=0.7, *;q=0.2\r\n" .
            "Content-Type: application/json\r\n" .
            "User-Agent: Offer Generator\r\n"
    ]
];

$context = stream_context_create($opts);

set_error_handler(
    function ($severity, $message, $file, $line) {
        $ret = [];
        $ret["ok"] = false;
        $ret["value"] = -1;
        $ret["msg"] = $message . "  \n" .  $severity . "  \n" . $file . "  \n" . $line;
        echo(json_encode($ret, JSON_UNESCAPED_UNICODE));
        die();
    }
);

$auftragsNummerInfoJson = file_get_contents($DBServerErmittlerUrl, false, $context);

restore_error_handler();

echo($auftragsNummerInfoJson);
