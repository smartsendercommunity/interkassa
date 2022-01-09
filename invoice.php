<?php

// v1   09.01.2022
// Powered by M-Soft
// https://t.me/mufik

ini_set('max_execution_time', '1700');
set_time_limit(1700);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: application/json');
header('Content-Type: text/html; charset=utf-8');

http_response_code(200);

//--------------


include ('config.php');

$body = '';
if ($_GET["ssId"] == NULL) {
    $result["state"] = false;
    $body = $body.'<p>Не найден идентификатор пользователя</p>';
}
if ($_GET["amount"] == NULL) {
    $result["state"] = false;
    $body = $body.'<p>Не найдена сума платежа</p>';
} else {
    $_GET["amount"] = str_replace(array(" ", ","), array("", "."), $_GET["amount"]);
}
if ($_GET["currency"] == NULL) {
    $result["state"] = false;
    $body = $body.'<p>Не найдена валюта платежа</p>';
}
if ($_GET["description"] == NULL) {
    $result["state"] = false;
    $body = $body.'<p>Не найдено описание платежа</p>';
}
if ($_GET["action"] == NULL) {
    $result["state"] = false;
    $body = $body.'<p>Не найдено название действия</p>';
}
if ($result["state"] === false) {
    $body = '<html><head><title>Ошибка платежа</title></head><body style="margin:50px"><h2 style="color:red;margin:20px">Ошибка платежа</h2>'.$body.'</body></html>';
    echo $body;
    exit;
}

$form["ik_co_id"] = $public_key;
$form["ik_pm_no"] = $_GET["ssId"]."_".mt_rand(1000000, 9999999);
$form["ik_am"] = $_GET["amount"];
$form["ik_cur"] = $_GET["currency"];
$form["ik_desc"] = $_GET["description"];
$form["ik_ia_u"] = $url."/callback.php";
$form["ik_ia_m"] = "POST";
$form["ik_x_ssId"] = $_GET["ssId"];
$form["ik_x_trigger"] = $_GET["action"];

// Sigmature
$formData = $form;
ksort($formData, SORT_STRING);
array_push($formData, $private_key);
$signString = implode(':', $formData);
$form["ik_sign"] = base64_encode(hash('sha256', $signString, true));

// Creating form
$data = '<html><head><title>Redirect</title></head><body onload="document.payment.submit()"><form name="payment" method="post" action="https://sci.interkassa.com/" accept-charset="UTF-8">';
foreach ($form as $fKey => $fValue) {
    $data = $data.'<input type="hidden" name="'.$fKey.'" value="'.$fValue.'"/>';
}
$data = $data.'</form></body></html>';

echo $data;









