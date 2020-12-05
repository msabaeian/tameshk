<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 6/4/2017
 * Time: 1:54 AM
 */
require_once 'config.inc.php';
require_once 'functions.php';
require_once './API/V1/api.order.function.php';
require_once './API/V1/payment.functions.php';
@$pid = $_GET['pid'];
if(check_set_empty(@$pid)){
    $pid = intval($pid);
    $payment = query("SELECT * FROM `payments` WHERE `payment_id` = ".$pid);
    if($payment->rowCount() == 1){
        $payment = $payment->fetch();
        if(intval($payment['payment_status']) == 1){
            $order_status = intval(query("SELECT `order_status` as os FROM `orders` WHERE `order_id` = ".$payment['payment_order_id'])->fetch()['os']);
            if($order_status == 1){
                $order['order_status'] = 2;
                $order['order_date'] = get_date();
                query(queryUpdate('orders',$order," WHERE `order_id` = ".$payment['payment_order_id']));
                
                $order_discount = intval(query("SELECT `order_discount_id` as odi FROM `orders` WHERE `order_id` = ".$payment['payment_order_id']));
                if($order_discount > 0){
                    query("UPDATE `discounts` SET `discount_total_use`=`discount_total_use`-1 WHERE `discount_id` = ".$order_discount);
                }
                $msg_type = 1;
                $msg = "پرداخت شما با موفقیت انجام شد، صفارش شما به زودی به دستتان خواهد رسید";
                
                
                
            }else{
                $msg_type = 1;
                $msg = "پرداخت شما قبلا تایید شده است.";
            }
        }else{
            /*$MerchantID = MerchantID;
            $Amount = $payment['payment_amount']; //Amount will be based on Toman
            $Authority = $payment['payment_authority'];
            $client = new SoapClient('https://www.zarinpal.com/pg/services/WebGate/wsdl', ['encoding' => 'UTF-8']);

            $result = $client->PaymentVerification(
                [
                    'MerchantID' => $MerchantID,
                    'Authority' => $Authority,
                    'Amount' => $Amount,
                ]
            ); */
            $transId = $payment['payment_authority'];
            $result = json_decode(verify(API_GETWAY,$transId));
            if ($result->status == 1 && $result->amount == intval($payment['payment_amount'])) {
                $payment_up['payment_refid'] = $transId;
                query(queryUpdate('payments',$payment_up," WHERE `payment_id` = ".$pid));
                $msg_type = 1;
                $msg = "پرداخت شما با موفقیت انجام شد، صفارش شما به زودی به دستتان خواهد رسید"."<br>"."کد رهگیری پرداخت: ".$transId;
                
                $order['order_status'] = 2;
                $order['order_date'] = get_date();
                query(queryUpdate('orders',$order," WHERE `order_id` = ".$payment['payment_order_id']));
                
                $order_discount = intval(query("SELECT `order_discount_id` as odi FROM `orders` WHERE `order_id` = ".$payment['payment_order_id']));
                if($order_discount > 0){
                    query("UPDATE `discounts` SET `discount_total_use`=`discount_total_use`-1 WHERE `discount_id` = ".$order_discount);
                }
                
            } else {
                $msg_type = 0;
                $msg =
                                "پرداخت موفقیت آمیز نبوده است. در صورت کسر مبلغ از حساب شما طی 48 ساعت آینده مبلغ توسط بانک عودت خواهد شد."."<br>"."کد رهگیری پرداخت: ".$transId;

                
                if(intval($payment['payment_balance']) > 0 && $payment['payment_refid'] == ""){
                    $data = intval($payment['payment_balance']);
                    $balacne = query("SELECT `user_balance` as ub FROM `users` WHERE `users_ID` = ".$payment['payment_user_id'])->fetch()['ub'];
                    $add['user_balance'] = intval($balacne+$data);
                    query(queryUpdate('users',$add,' WHERE `users_ID` = '. $uid));
                }
                $payment_up['payment_refid'] = $transId;
                query(queryUpdate('payments',$payment_up," WHERE `payment_id` = ".$pid));
            }
        }

    }else{
        $msg_type = 0;
        $msg = "خطایی از سوی سرور رخ داده است. لطفا با پشتیبانی تماس حاصل فرمایید.";
    }
}
if(isset($msg_type) && isset($msg)):
    if($msg_type == 1){
        $color = "#4caf50";
        $logo = "✔";
    }else{
        $color = "#e91e63";
        $logo = "✖";
    }
 else:
    $msg = "شما مجوز دسترسی به این صفحه را ندارید!";
     $color = "#e91e63";
     $logo = "✖";
endif; ?>

<html>
<head>
    <meta charset="utf-8">
    <title>بررسی پرداخت</title>
</head>
<body bgcolor="<?php echo $color; ?>" dir="rtl">
<center><p style="color:#fff; font-size: 60pt; text-shadow: 0 0 5px #000;"><?php echo $logo; ?></p></center>
<center><p style="color:#fff; font-size: 14pt; text-shadow: 0 0 5px #000; line-height: 35px;"><?php echo $msg; ?></p></center>
</body>
</html>