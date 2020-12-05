<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 6/28/2017
 * Time: 12:39 AM
 */

function sendPayment($api, $amount, $redirect, $factorNumber=null) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://pay.ir/payment/send');
	curl_setopt($ch, CURLOPT_POSTFIELDS,"api=$api&amount=$amount&redirect=$redirect&factorNumber=$factorNumber");
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	$res = curl_exec($ch);
	curl_close($ch);
	return $res;
}

function verify($api, $transId) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://pay.ir/payment/verify');
	curl_setopt($ch, CURLOPT_POSTFIELDS, "api=$api&transId=$transId");
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	$res = curl_exec($ch);
	curl_close($ch);
	return $res;
}
?>