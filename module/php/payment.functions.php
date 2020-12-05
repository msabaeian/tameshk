<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 5/11/2017
 * Time: 1:39 PM
 */

/*

            if($pr['order_status'] == 0){
                $type = "انجام نشد";
            }else if($pr['order_status'] == 1){
                $type = "در انتظار پرداخت";
            }else if($pr['order_status'] == 2){
                $type = "در حال ارسال";
            }else if($pr['order_status'] == 3){
                $type = "انجام شده";
            }else if($pr['order_status'] == 4){
                $type = "پرداخت انجام نشد";
            }else if($pr['order_status'] == 5){
                $type = "سفارش لغو شده";
            }

 */

function payment_do($type,$pid){
    $pid = intval($pid);

    switch ($type){
        case "done";
            $payment['payment_status'] = 1;
            query(queryUpdate('payments',$payment,' WHERE `payment_id` = '.$pid));

            $iod = query("SELECT * FROM `payments` WHERE `payment_id` = ".$pid);
            $iod = $iod->fetch();
            $uid = $iod['payment_user_id'];
            $payment_amount = $iod['payment_amount'];
            $iod = $iod['payment_order_id'];
            if($iod){
                order_do('doing',$iod);
            }else{
                user_do('add_balance',$uid,$payment_amount);
            }

            break;
    }
}

function order_do($type,$oid,$uid = 0){
    $oid = intval($oid);
    $uid = intval($uid);
    switch ($type){
        case "cancel";
            $cancel['order_status'] = 5;
            query(queryUpdate('orders',$cancel, 'WHERE `order_id` = '.$oid));
            $tt = query("SELECT `order_total` as tt FROM `orders` WHERE `order_id` =  ".$oid)->fetch()['tt'];
            user_do('add_balance',$uid,$tt);
            break;
        case "doing";
            $doing['order_status'] = 2;
            query(queryUpdate('orders',$doing, 'WHERE `order_id` = '.$oid));
            break;
    }
}

function user_do($type,$uid,$data = 0){
    $uid = intval($uid);
    switch ($type){
        case "add_balance";
            $data = intval($data);
            $balacne = query("SELECT `user_balance` as ub FROM `users` WHERE `users_ID` = ".$uid)->fetch()['ub'];
            $add['user_balance'] = intval($balacne+$data);
            query(queryUpdate('users',$add,' WHERE `users_ID` = '. $uid));
            break;
    }
}
?>