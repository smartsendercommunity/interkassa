<?php

// Данные интеграции с InterKassa
$public_key = "";
$private_key = "";
$private_test_key = "";
$ss_token = "";

// Сервисные данные
$log_url = "https://webhook.site/interkassa";
$dir = dirname($_SERVER["PHP_SELF"]);
$url = ((!empty($_SERVER["HTTPS"])) ? "https" : "http") . "://" . $_SERVER["HTTP_HOST"] . $dir;
$url = explode("?", $url);
$url = $url[0];