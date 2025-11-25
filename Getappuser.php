<?php
include(__DIR__ . '/../includes/functions.php');
$mes = $db->select('welcome', '*', 'id = :id', '', [':id' => 1]);
$mec = $db->select('macl', '*', 'id = :id', '', [':id' => 1]);
$theme = $db->select('theme', '*', 'id = :id', '', [':id' => 1]);
$update = $db->select('appupdate', '*', 'id = :id', '', [':id' => 1]);
$licens = $db->select('licens', '*', 'id = :id', '', [':id' => 1]);
$demo = $db->select('demopls', '*', 'id = :id', '', [':id' => 1]);
$lthemes = $db->select('logintheme', '*', 'id = :id', '', [':id' => 1]);
$logintxt = $db->select('logintext', '*', 'id = :id', '', [':id' => 1]);
error_reporting(0);
const ALLOWED_CHARACTERS = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
$message_one;
$message_two;
$logintxt_one;
$logintxt_tow;
$mac_lenth;
$currunt_theme;
$update_version;
$update_url;
$licens_key;
$demoPlaylist;
$loginthemes;

if (empty($demo[0]['mdns'] ?? null)){
	$demoPlaylist = '[]';
}else{
	
	$urlsdemo = [
    [
        "is_protected" => "1",
        "id" => "11f1c6c52a212ad96288f8c004fb9148RTXREBRAND",
        "url" => $demo[0]['mdns']."/get.php?username=".$demo[0]['muser']."&password=".$demo[0]['mpass']."&type=m3u_plus&output=ts",
        "name" => $demo[0]['mplname'],
        "created_at" => "2023-04-15 00:06:09",
        "updated_at" => "2023-04-15 00:06:09",
    ],
	];
	$demoPlaylist = json_encode($urlsdemo);  
}

if (empty($lthemes[0]['themelog'] ?? null)){
	$loginthemes = 'theme_0';
}else{
	$loginthemes = $lthemes[0]['themelog'];
}


if (empty($licens[0]['lkey'] ?? null)){
	$licens_key = 'null';
}else{
	$licens_key = $licens[0]['lkey'];
}


if (empty($update[0]['nversion'] ?? null) || empty($update[0]['nurl'] ?? null)){
	$update_version = '3.9';
	$update_url = 'https://t.me/SaNoJRTX';
}else{
	$update_version = $update[0]['nversion'];
	$update_url = $update[0]['nurl'];
}

if	(empty($logintxt[0]['logintitial'] ?? null)){
	$logintxt_one = '';
}else{
	$logintxt_one = base64_encode($logintxt[0]['logintitial']);
}

if	(empty($logintxt[0]['loginsubtitial'] ?? null)){
	$logintxt_tow = '';
}else{
	$logintxt_tow = base64_encode($logintxt[0]['loginsubtitial']);
}


if	(empty($mes[0]['message_one'] ?? null)){
	$message_one = '';
}else{
	$message_one = base64_encode($mes[0]['message_one']);
}

if (empty($mes[0]['message_two'] ?? null)){
	$message_two = '';
}else{
	$message_two = base64_encode($mes[0]['message_two']);
}


if ($mec[0]['mac_length'] == 0 || empty($mec[0]['mac_length'])){
	$mac_lenth = 12;
}else{
	$mac_lenth = $mec[0]['mac_length'];
}

if ($theme[0]['theme_no'] == '' || empty($theme[0]['theme_no'])){
	$currunt_theme = 'theme_0';
}else{
	$currunt_theme = $theme[0]['theme_no'];
}


function getDecodedString($str) {
    $encryptKeyPosition = getEncryptKeyPosition(substr($str, -2, 1));
    $encryptKeyPosition2 = getEncryptKeyPosition(substr($str, -1));
    $substring = substr($str, 0, -2);
    return trim(utf8_decode(base64_decode(substr($substring, 0, $encryptKeyPosition) . substr($substring, $encryptKeyPosition + $encryptKeyPosition2))));
}

function getEncryptKeyPosition($str) {
    return strpos(ALLOWED_CHARACTERS, $str);
}

function getEncodedString($str) {
    $encryptKeyPosition = getEncryptKeyPosition(substr($str, -2, 1));
    $encryptKeyPosition2 = getEncryptKeyPosition(substr($str, -1));
    $encodedString = base64_encode(utf8_encode($str));
    $substring = substr($encodedString, 0, $encryptKeyPosition) . substr($encodedString, $encryptKeyPosition + $encryptKeyPosition2);
    return $substring . substr(ALLOWED_CHARACTERS, $encryptKeyPosition, 1) . substr(ALLOWED_CHARACTERS, $encryptKeyPosition2, 1);
}

function lang(){
    $language_json = file_get_contents('./json/language.json');
    return $language_json;
}
function getUserData($mac_address){
    $db = new SQLite3('./.db.db');
    $mac_address = strtolower($mac_address);
    $ibo_query = $db->query('SELECT * FROM ibo WHERE LOWER(mac_address)="' . $mac_address . '"');
    $urls = [];
    while ($ibo_row = $ibo_query->fetchArray()) {
        $urls[] = [
            'is_protected' => $ibo_row['protection'], 
            'id' => md5($ibo_row['password']).'RTXREBRAND' . $ibo_row['id'], 
            'url' => $ibo_row['url']."/get.php?username=".$ibo_row['username']."&password=".$ibo_row['password']."&type=m3u_plus&output=ts", 
            'name' => $ibo_row['title'], 
            'created_at' => '2023-04-15 00:06:09',
            'updated_at' => '2023-04-15 00:06:09'
        ];
    }
    //return json_encode($urls);
    return [
        'urls' => json_encode($urls)
    ];
}

function getExpired($mac_address){
    $db = new SQLite3('./.db.db');
    $mac_address = strtolower($mac_address);
    $ibo_query = $db->query('SELECT * FROM trial WHERE LOWER(mac_address)="' . $mac_address . '"');
    $expire_date = "";
    while ($ibo_row = $ibo_query->fetchArray()) {
        $expire_date = $ibo_row['expire_date'];
    }

    if (!empty($expire_date)) {
        return $expire_date; // Return the fetched expiry date if it's not empty
    } else {
        return "2033-03-13"; // Return default expiry date if no expiry date is found
    }
}


function escapeUrl($url) {
  return addcslashes($url, '/\\');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $post_data = file_get_contents('php://input');
    
    // decode the JSON data
    $json_data = json_decode($post_data);
    $json_data = $json_data->data;
    $json_data = getDecodedString($json_data);
    $json_data = json_decode($json_data, true);
    $mac_address = getDecodedString($json_data['app_device_id']);
	$mac_address = substr($mac_address, 0, $mac_lenth); // Limiting to the first 6 characters
	$mac_address = chunk_split($mac_address, 2, ':');
	$mac_address = rtrim($mac_address, ':');

    

   
    $result = getUserData($mac_address);
	$expire_date_p = getExpired($mac_address);
    $userData = $result['urls'];
    
    if($userData !== "[]"){
        $output_json = '{
        "android_version_code":"'.$update_version.'",
        "apk_url":"'.$update_url.'",
        "device_key":"136115",
        "notification_tital":"'.$message_one.'",
        "notification_content":"'.$message_two.'",
        "login_tital":"'.$logintxt_one.'",
        "login_content":"'.$logintxt_tow.'",
        "licen_key":"'.$licens_key.'",
        "expire_date":"'.$expire_date_p.'",
        "is_google_paid":true,
        "app_themes":"'.$currunt_theme.'",
        "log_themes":"'.$loginthemes.'",
        "is_trial":0,
        "notification": {
        	"title": "IBO PLAYER by appsnscripts",
        	"content": "ibo player "
    	},
        "urls":'.$userData.',
        "mac_registered":true,
        "themes":"",
        "trial_days":360,
        "plan_id":"03370629",
        "mac_address":"'.$mac_address.'",
        "pin":"0000",
        "price":"0",
        "app_version":"'.$update_version.'",
        "is_show": true,
    	"is_ib_show": true,
    	"subtitleAPIKey": "elTMMQhCQhUOLL1m5Y713lobS7o1cOGt",
    	"subtitleAPIKeySS": "elTMMQhCQhUOLL1m5Y713lobS7o1cOGt",
        "languages":[' . lang() . '],
        "apk_link":"'.$update_url.'"}'; 

        $output_json = '{"data":"'.getEncodedString($output_json).'"}';
    
    
    }else{
        
    	$output_json = '{
        "android_version_code":"'.$update_version.'",
        "apk_url":"'.$update_url.'",
        "device_key":"136115",
        "notification_tital":"'.$message_one.'",
        "notification_content":"'.$message_two.'",
        "login_tital":"'.$logintxt_one.'",
        "login_content":"'.$logintxt_tow.'",
        "licen_key":"'.$licens_key.'",
        "expire_date":"'.$expire_date_p.'",
        "is_google_paid":true,
        "app_themes":"'.$currunt_theme.'",
        "log_themes":"'.$currunt_theme.'",
        "is_trial":0,
        "notification": {
        	"title": "IBO PLAYER by appsnscripts",
        	"content": "ibo player "
    	},
        "urls":'.$demoPlaylist.',
        "mac_registered":true,
        "themes":"",
        "trial_days":360,
        "plan_id":"03370629",
        "mac_address":"'.$mac_address.'",
        "pin":"0000",
        "price":"0",
        "app_version":"'.$update_version.'",
        "is_show": true,
    	"is_ib_show": true,
    	"subtitleAPIKey": "elTMMQhCQhUOLL1m5Y713lobS7o1cOGt",
    	"subtitleAPIKeySS": "elTMMQhCQhUOLL1m5Y713lobS7o1cOGt",
        "languages":[' . lang() . '],
        "apk_link":"'.$update_url.'"}'; 
        
        $output_json = '{"data":"'.getEncodedString($output_json).'"}';
    }

    http_response_code(200);
    header('HTTP/1.1 200 OK');
    header('Server: Apache');
    header('Cache-Control: no-cache, private');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Max-Age: 86400');
    header('Access-Control-Allow-Headers: ');
    header('Access-Control-Allow-Method: ');
    header('Access-Control-Allow-Credentials: true');
    header('Connection: close');
    header('Content-Type: application/json');
    echo $output_json;

  }

