<?php

// v1   19.11.2021
// Powered by Smart Sender
// https://smartsender.com

ini_set('max_execution_time', '1700');
set_time_limit(1700);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: application/json');
header('Content-Type: application/json; charset=utf-8');

http_response_code(200);

//--------------

$input = json_decode(file_get_contents('php://input'), true);
include ('config.php');

// Functions
{
function send_forward($inputJSON, $link){
	
$request = 'POST';	
		
$descriptor = curl_init($link);

 curl_setopt($descriptor, CURLOPT_POSTFIELDS, $inputJSON);
 curl_setopt($descriptor, CURLOPT_RETURNTRANSFER, 1);
 curl_setopt($descriptor, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
 curl_setopt($descriptor, CURLOPT_CUSTOMREQUEST, $request);

    $itog = curl_exec($descriptor);
    curl_close($descriptor);

   		 return $itog;
		
}
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
}



echo json_encode($data);

$data = $_POST;
unset($data["ik_sign"]);
ksort($data, SORT_STRING);
$testData = $data;
array_push($data, $private_key);
array_push($testData, $private_test_key);
$signString = implode(':', $data);
$testSignString = implode(':', $testData);
$sign = base64_encode(hash('sha256', $signString, true));
$testSign = base64_encode(hash('sha256', $testSignString, true));
if ($_POST["ik_sign"] == $sign) {
    $result["state"] = "true sign";
    $trigger["name"] = $_POST["ik_x_trigger"];
} else if ($_POST["ik_sign"] == $testSign) {
    $result["state"] = "true testSign";
    $trigger["name"] = "test_".$_POST["ik_x_trigger"];
} else {
    $result["state"] = "false sign";
}

if ($_POST["ik_inv_st"] == "success" && $trigger != NULL) {
    $result["SmartSender"] = json_decode(send_bearer("https://api.smartsender.com/v1/contacts/".$_POST["ik_x_ssId"]."/fire", $ss_token, "POST", $trigger), true);
}





$result["trigger"] = $trigger;
$result["post"] = $_POST;
send_forward(json_encode($result), $log_url);











