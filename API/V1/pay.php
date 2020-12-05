<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 6/4/2017
 * Time: 1:07 AM
 */
require_once 'api.functions.php';
require_once 'payment.functions.php';
@$oc = $_GET['oc'];
if(check_set_empty(@$oc) /* && check_set_empty(@$type) */){
	$type = "balance";
            $oc = preg_replace('/\s+/', '', cleanInput($oc));
            $type = cleanInput($type);
            $order = query("SELECT * FROM `orders` WHERE `order_code` = '".$oc."' AND `order_status` = 1");
            if($order->rowCount() == 1){
                $order = $order->fetch();
                $order_code = $order['order_code'];
                $user_id = intval($order['order_user_id']);
                $oid = $order['order_id'];
                if($type == "balance"){
                    $balance = intval(query("SELECT `user_balance` as bal FROM `users` WHERE `users_ID` = ".$user_id)->fetch()['bal']);
                    if($balance >= $order['order_total']){
                        $payment['payment_amount'] =0;
                        $payment['payment_balance'] =$order['order_total'];
                        $payment['payment_status'] = 1;
                        query("UPDATE `users` SET `user_balance`=(`user_balance`-".$order['order_total'].") WHERE `users_ID` = ".$user_id);
                    }else{
                        $payment['payment_amount'] = $order['order_total']-$balance;
                        $payment['payment_balance'] =$balance;
                        query("UPDATE `users` SET `user_balance`=0 WHERE `users_ID` = ".$user_id);
                    }
                    
                }else{
                    $payment['payment_amount'] = $order['order_total'];
                    $payment['payment_balance'] =0;
                }

                $payment['payment_date'] = get_date();
                require_once ('../../module/php/jdf.php');
                $payment['payment_jdate'] = jdate("Y-m-d H:i:s",time());
                $payment['payment_user_id'] = $user_id;
                $payment['payment_order_id'] = $oid;
                query(queryInsert('payments',$payment));
                global $db;
                $payment_id = $db->lastInsertId();
                if($payment['payment_amount']==0){
                    $order['payment_status'] = 1;
                    query(queryUpdate('payments',$order," WHERE `payment_id` = ".$payment_id));
                    header("Location: http://msabaeian.ir/Tameshk/verify.php?pid=".$payment_id);
                }else{
                    /*
                    $MerchantID = MerchantID; //Required
                    $Amount = $payment['payment_amount']; //Amount will be based on Toman - Required
                    $Description = 'پرداخت صورتحساب خرید'; // Required
                    $CallbackURL = 'http://msabaeian.ir/Tameshk/verify/'.$payment_id; // Required
                    $client = new SoapClient('https://www.zarinpal.com/pg/services/WebGate/wsdl', ['encoding' => 'UTF-8']);
                    $result = $client->PaymentRequest(
                        [
                            'MerchantID' => $MerchantID,
                            'Amount' => $Amount,
                            'Description' => $Description,
                            'CallbackURL' => $CallbackURL,
                        ]
                    );
                    if ($result->Status == 100) {
                        $payment_update['payment_authority'] = $result->Authority;
                        query(queryUpdate('payments',$payment_update," WHERE `payment_id` =".$payment_id));
                        header('Location: https://www.zarinpal.com/pg/StartPay/'.$result->Authority);
                    } else {
                        echo'Error!';
                    }
                    */
                    
                    $amount = $payment['payment_amount']."0";
                    $redirect = 'https://msabaeian.ir/Tameshk/verify.php'.$payment_id;
                    $factorNumber = $order_code;
                    $result = json_decode(sendPayment(API_GETWAY,$amount,$redirect,$factorNumber));
                    
                    if($result->status) {
                        $payment_update['payment_authority'] = $result->transId;
                        query(queryUpdate('payments',$payment_update," WHERE `payment_id` =".$payment_id));
                    	$go = "https://pay.ir/payment/gateway/$result->transId";
                    	header("Location: $go");
                    } else {
                    	echo $result->errorMessage;
                    }
                }
            }else{
                echo response("ORDER_NOT_FOUND");
            }
}