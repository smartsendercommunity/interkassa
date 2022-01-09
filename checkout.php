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
function send_bearer($url, $token, $type = "GET", $param = []){
	
		
$descriptor = curl_init($url);

 curl_setopt($descriptor, CURLOPT_POSTFIELDS, json_encode($param));
 curl_setopt($descriptor, CURLOPT_RETURNTRANSFER, 1);
 curl_setopt($descriptor, CURLOPT_HTTPHEADER, array('User-Agent: M-Soft Integration', 'Content-Type: application/json', 'Authorization: Bearer '.$token)); 
 curl_setopt($descriptor, CURLOPT_CUSTOMREQUEST, $type);

    $itog = curl_exec($descriptor);
    curl_close($descriptor);

   		 return $itog;
		
}


include ('config.php');

$body = '';
if ($_GET["ssId"] == NULL) {
    $result["state"] = false;
    $body = $body.'<p>Не найден идентификатор пользователя</p>';
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
$form["ik_desc"] = $_GET["description"];
$form["ik_ia_u"] = $url."/callback.php";
$form["ik_ia_m"] = "POST";
$form["ik_x_ssId"] = $_GET["ssId"];
$form["ik_x_trigger"] = $_GET["action"];

// Получение списка товаров в корзине пользователя
$cursor = json_decode(send_bearer("https://api.smartsender.com/v1/contacts/".$_GET["ssId"]."/checkout?page=1&limitation=20", $ss_token), true);
if ($cursor["error"] != NULL && $cursor["error"] != 'undefined') {
    $body = $body.'<p>Ошибка получения данных из SmartSender</p>';
    if ($cursor["error"]["code"] == 404 || $cursor["error"]["code"] == 400) {
        $body = $body.'<p>Пользователь не найден. Проверте правильность идентификатора пользователя и приналежность токена к текущему проекту.</p>';
    } else if ($cursor["error"]["code"] == 403) {
        $body = $body.'<p>Токен проекта SmartSender указан неправильно. Проверте правильность токена.</p>';
    }
    $body = '<html><head><title>Ошибка платежа</title></head><body style="margin:50px"><h2 style="color:red;margin:20px">Ошибка платежа</h2>'.$body.'</body></html>';
    echo $body;
    exit;
} else if (empty($cursor["collection"])) {
    $body = $body.'<p>Корзина пользователя пустая. Для тестирования добавте товар в корзину.</p>';
    $body = '<html><head><title>Ошибка платежа</title></head><body style="margin:50px"><h2 style="color:red;margin:20px">Ошибка платежа</h2>'.$body.'</body></html>';
    echo $body;
    exit;
}
$pages = $cursor["cursor"]["pages"];
$count = 1;
for ($i = 1; $i <= $pages; $i++) {
    $checkout = json_decode(send_bearer("https://api.smartsender.com/v1/contacts/".$_GET["ssId"]."/checkout?page=".$i."&limitation=20", $ss_token), true);
	$essences = $checkout["collection"];
	$form["ik_cur"] = $essences[0]["currency"];
	foreach ($essences as $product) {
	    $goods["name"] = $product["product"]["name"];
	    $goods["description"] = $product["name"];
	    $goods["currency"] = $product["currency"];
	    $goods["amount"] = $product["price"];
	    $goods["qty"] = $product["pivot"]["quantity"];
	    //$form["ik_products"][] = $goods;
	    unset($goods);
	    $count ++;
		$summ[] = $product["pivot"]["quantity"]*$product["cash"]["amount"];
    }
}
$form["ik_am"] = array_sum($summ);

// Sigmature
$formData = $form;
ksort($formData, SORT_STRING);
array_push($formData, $private_key);
$signString = implode(':', $formData);
$form["ik_sign"] = base64_encode(hash('sha256', $signString, true));

// Creating form
$data = '<html><head><title>Redirect</title></head><body onload="document.payment.submit()"><form name="payment" method="post" action="https://sci.interkassa.com/" accept-charset="UTF-8">';
// onload="document.payment.submit()"
foreach ($form as $fKey => $fValue) {
    $data = $data.'<input type="hidden" name="'.$fKey.'" value="'.$fValue.'"/>';
}
$data = $data.'</form></body></html>';

echo $data;









