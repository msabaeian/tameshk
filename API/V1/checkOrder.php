<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 6/29/2017
 * Time: 4:24 PM
 */
require_once 'api.functions.php';
    $sql = "SELECT * FROM `orders` as orders
	LEFT JOIN `shops` as shops on shops.shop_id = orders.order_shop_id
    LEFT JOIN users as users on users.users_ID = shops.shop_admin
 WHERE orders.`order_status` = 2";
    foreach(query($sql) as $pr){
        $mine =  round(abs( strtotime(date("Y-m-d H:i:s",time())) - strtotime($pr['order_date'])) / 60,2);
        if($mine >= 3 AND $mine < 6){
            $token = $pr['order_code'];
            $token2 = $pr['shop_name'];
            $token3 = $pr['user_phone'];
            sendSms("09378470956",$token,'orderLate',$token2,$token3);
        }
    }
?>