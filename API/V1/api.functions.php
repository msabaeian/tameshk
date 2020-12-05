<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 5/30/2017
 * Time: 7:03 PM
 */

require_once './../../config.inc.php';
require_once './../../functions.php';

define("AndroidLastVersionCode",1);
define("AndroidLastVersionCodeWorks",1);

foreach ($_POST as $pr=>$key){
    IF(!is_array($key)){
        $$pr = cleanInput($key);
    }else{
        $$pr = $key;
    }

}
function echo_json($array){
    echo json_encode($array);
}

function response($type){
    switch ($type){
        case 'USER_NOT_FOUND':
            return "USER_NOT_FOUND";
            break;
        case 'USER_LOCK':
            return "USER_LOCK";
            break;
        case 'USER_PHONE_EXIST':
            return "USER_PHONE_EXIST";
            break;
        case 'ACTIVE_CODE_SEND':
            return "ACTIVE_CODE_SEND";
            break;
        case 'WAIT_4_MIN':
            return "WAIT_4_MIN";
            break;
        case 'WRONG_PHONE_OR_CODE':
            return "WRONG_PHONE_OR_CODE";
            break;
        case 'REGISTER_COMPLETE':
            return "REGISTER_COMPLETE";
            break;
        case 'UPDATED':
            return "UPDATED";
            break;
        case 'CLOSE':
            return "CLOSE";
            break;
        case 'NOT_FOUND':
            return "NOT_FOUND";
            break;
        case 'SHOP_CLOSED':
            return "SHOP_CLOSED";
            break;
        case 'SHOP_NOT_FOUND':
            return "SHOP_NOT_FOUND";
            break;
        case 'DISCOUNT_NOT_FOUND':
            return "DISCOUNT_NOT_FOUND";
            break;
        case 'NOT_FOR_THIS_CITY':
            return "NOT_FOR_THIS_CITY";
            break;
        case 'NOT_FOR_THIS_SHOP':
            return "NOT_FOR_THIS_SHOP";
            break;
        case 'NOT_FOR_THIS_USER':
            return "NOT_FOR_THIS_USER";
            break;
        case 'TIME_IS_LEFT':
            return "TIME_IS_LEFT";
            break;
        case 'END_OF_USE_DISCOUNT':
            return "END_OF_USE_DISCOUNT";
            break;
        case 'COMMENT_SUBMIT':
            return "COMMENT_SUBMIT";
            break;
        case 'LIKE_ADDED':
            return "LIKE_ADDED";
            break;
        case 'ORDER_NOT_FOUND':
            return "ORDER_NOT_FOUND";
            break;
        case 'MESSAGE_SEND':
            return "MESSAGE_SEND";
            break;
        case 'MESSAGE_NOT_SEND':
            return "MESSAGE_NOT_SEND";
            break;
        case "NOT_ANY_SHOP_ADMIN":
            return "NOT_ANY_SHOP_ADMIN";
            break;

    }
}

function sendSms($to,$token = "",$type = 'code',$token2 = '',$token3 = ''){
    $token = cleanInput($token);
    $token2 = cleanInput($token2);
    $token3 = cleanInput($token3);
    $to = cleanInput($to);
    /* $url = 'http://www.afe.ir/WebService/V4/BoxService.asmx?wsdl';
    $method = 'SendMessage';
    $param = array('Username' => SMS_USERNAME,'Password' => SMS_PASSWORD,'Number' => SMS_NUMBER,'Mobile' => array("$to"),'Message' => "$content",'Type' => "3");
    define($security,1);
    include_once("../../module/php/SMS/connection.php");
    $request = new connection($url,$method,$param);
    $message = $request->connect();
    $request->__destruct();
    unset($url,$method,$param,$request);
    */
    $token2 = (!empty($token2)) ? '&token2='.$token2 : '';
    $token3 = (!empty($token3)) ? '&token3='.$token3 : '';
    $ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://api.kavenegar.com/v1/Your-API-Key/verify/lookup.json');
	curl_setopt($ch, CURLOPT_POSTFIELDS,"receptor=$to&token=$token&template=$type".$token2.$token3);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	$res = curl_exec($ch);
	curl_close($ch);
    
    global $db;
    $sms['sms_content'] = $token;
    $sms['sms_date'] = get_date();
    $sms['sms_to'] = $to;
    query(queryInsert('sms_log',$sms));
    //RETURN $message;
}

function canSendSMS($phone){
    global $db;
    $phone = phone($phone);
    $result = $db->query("SELECT * FROM `sms_log` WHERE `sms_to` = '$phone' ORDER BY `sms_id` DESC LIMIT 1");
    if($result->rowCount() == 1){
        $fetchDate = $result->fetch();
        $mine =  round(abs( strtotime(date("Y-m-d H:i:s",time())) - strtotime($fetchDate['sms_date'])) / 60,2);
        if($mine >= 4){
            return true;
        }else{
            echo false;
        }
    }else{
        return true;
    }
}
