<?php
include 'includes/header.php';
require_once './../module/php/payment.functions.php';
permission("discount",true);
$type = isset($_GET['type']) ? $_GET['type'] : "all";
$type_page = isset($_GET['type']) ? $_GET['type'] : "all";
if(isset($action)){
    if($action == "done" && check_set_empty($pid)){
        payment_do('done',intval($pid));
    }
}
if(isset($type)){
    $PHP_SELF = $PHP_SELF."?type=".$type;

    if($type=="all"){
        $count = query("SELECT COUNT(`payment_id`) as pay_count FROM `payments` ")->fetch()['pay_count'];
        $pages = intval($count/40);
        $page = isset($page) ? intval($page) : 0;
        $offset = $page*40;
        echo ' 
        <header class="top"> <i class="fa fa-dollar" aria-hidden="true"></i> پرداخت ها <form action="'.$PHP_SELF.'"><input name="s" placeholder="جستجو"><input type="submit" hidden> </form></header>
        <table class="tbl">
        <tr class="top">
            <td>#</td>
            <td>هزینه</td>
            <td>کد اعتبار</td>
            <td>کد رهگیری</td>
            <td>تاریخ</td>
            <td>نوع</td>
            <td>وضعیت</td>
            <td>عملیات</td>
        </tr>
        ';
        // ✔ ✖
        @$s = cleanInput($_GET['s']);
        $sql = (isset($_GET['s'])) ? "SELECT * FROM `payments`  WHERE `payment_order_id` = '$s' ORDER BY `payment_id` DESC LIMIT 40 OFFSET $offset" :
    "SELECT * FROM `payments` ORDER BY `payment_id` DESC LIMIT 40 OFFSET $offset";

        $result = query($sql);
        foreach ($result as $pr){
            //$active = ($pr['city_active']==0) ? "<b><a style='color: green !important' href='city.php?action=active&cityId=".$pr['city_id']."'>✔</a></b>" : "<b><a style='color: rgb(218, 0, 0) !important' href='city.php?action=unActive&cityId=".$pr['city_id']."'>✖</a></b>";

            if($pr['payment_order_id']){
                $type = '<a style="color:green !important;"  href="order.php?type=show&oid='.$pr["payment_order_id"].'">پرداخت سفارش</a>';
            }else{
                $type = '<a style="color:blue !important;" href="users.php?type=show&userID='.$pr["payment_user_id"].'">شارژ حساب کاربری</a>';
            }
            if($pr['payment_status']){
                $status = '<b><font color="green">✔</font></b>';
                $active='-';
            }else{
                $status = '<b><font color="red">✖</font></b>';
                $active='<a href="'.ADDR.'verify.php?pid='.$pr['payment_id'].'" target="_blank">بررسی پرداخت</a>';
            }
            echo '<tr>
                    <td>'.$pr['payment_id'].'</td>
                    <td>'.num(intval($pr["payment_amount"]+$pr["payment_balance"]),true).' تومان</td>
                    <td>'.num($pr["payment_authority"]).'</td>
                    <td>'.num($pr["payment_refid"]).' </td>
                    <td>'.$pr["payment_date"].' - '.$pr["payment_jdate"].'</td>
                    <td>'.$type.'</td>
                    <td>'.$status.'</td>
                    <td>'.$active.'</td>
                </tr>
            ';
        }
        echo "</table>
               
            ";
        for ($i=0;$i<=$pages;$i++){
            echo '<a class="a" href="payments.php?type='.$type_page.'&page='.$i.'">'.$i.'</a>';
        }
    }else if($type=="done"){
        $count = query("SELECT COUNT(`payment_id`) as pay_count FROM `payments` WHERE `payment_status`")->fetch()['pay_count'];
        $pages = intval($count/40);
        $page = isset($page) ? intval($page) : 0;
        $offset = $page*40;
        echo ' 
        <header class="top"> <i class="fa fa-dollar" aria-hidden="true"></i> پرداخت های انجام شده</header>
        <table class="tbl">
        <tr class="top">
            <td>#</td>
            <td>هزینه</td>
            <td>کد اعتبار</td>
            <td>کد رهگیری</td>
            <td>تاریخ</td>
            <td>نوع</td>
            <td>وضعیت</td>
            <td>عملیات</td>
        </tr>
        ';
        // ✔ ✖
        $result = query("SELECT * FROM `payments` WHERE `payment_status` ORDER BY `payment_id` DESC LIMIT 40 OFFSET $offset");
        foreach ($result as $pr){
            //$active = ($pr['city_active']==0) ? "<b><a style='color: green !important' href='city.php?action=active&cityId=".$pr['city_id']."'>✔</a></b>" : "<b><a style='color: rgb(218, 0, 0) !important' href='city.php?action=unActive&cityId=".$pr['city_id']."'>✖</a></b>";

            if($pr['payment_order_id']){
                $type = '<a style="color:green !important;"  href="order.php?type=show&oid='.$pr["payment_order_id"].'">پرداخت سفارش</a>';
            }else{
                $type = '<a style="color:blue !important;" href="users.php?type=show&userID='.$pr["payment_user_id"].'">شارژ حساب کاربری</a>';
            }
            if($pr['payment_status']){
                $status = '<b><font color="green">✔</font></b>';
                $active='-';
            }else{
                $status = '<b><font color="red">✖</font></b>';
                $active='<a href="payments.php?type='.$type_page.'&action=done&pid='.$pr['payment_id'].'">تایید پرداخت</a>';
            }
            echo '<tr>
                    <td>'.$pr['payment_id'].'</td>
                    <td>'.num($pr["payment_amount"],true).' تومان</td>
                    <td>'.num($pr["payment_authority"]).'</td>
                    <td>'.num($pr["payment_refid"]).' </td>
                    <td>'.$pr["payment_date"].' - '.$pr["payment_jdate"].'</td>
                    <td>'.$type.'</td>
                    <td>'.$status.'</td>
                    <td>'.$active.'</td>
                </tr>
            ';
        }
        echo "</table>
               
            ";
        for ($i=0;$i<=$pages;$i++){
            echo '<a class="a" href="payments.php?type='.$type_page.'&page='.$i.'">'.$i.'</a>';
        }
    }else if($type=="undone"){
        $count = query("SELECT COUNT(`payment_id`) as pay_count FROM `payments` WHERE !`payment_status`")->fetch()['pay_count'];
        $pages = intval($count/40);
        $page = isset($page) ? intval($page) : 0;
        $offset = $page*40;
        echo ' 
        <header class="top"> <i class="fa fa-dollar" aria-hidden="true"></i> پرداخت های انجام شده</header>
        <table class="tbl">
        <tr class="top">
            <td>#</td>
            <td>هزینه</td>
            <td>کد اعتبار</td>
            <td>کد رهگیری</td>
            <td>تاریخ</td>
            <td>نوع</td>
            <td>وضعیت</td>
            <td>عملیات</td>
        </tr>
        ';
        // ✔ ✖
        $result = query("SELECT * FROM `payments` WHERE !`payment_status` ORDER BY `payment_id` DESC LIMIT 40 OFFSET $offset");
        foreach ($result as $pr){
            //$active = ($pr['city_active']==0) ? "<b><a style='color: green !important' href='city.php?action=active&cityId=".$pr['city_id']."'>✔</a></b>" : "<b><a style='color: rgb(218, 0, 0) !important' href='city.php?action=unActive&cityId=".$pr['city_id']."'>✖</a></b>";

            if($pr['payment_order_id']){
                $type = '<a style="color:green !important;"  href="order.php?type=show&oid='.$pr["payment_order_id"].'">پرداخت سفارش</a>';
            }else{
                $type = '<a style="color:blue !important;" href="users.php?type=show&userID='.$pr["payment_user_id"].'">شارژ حساب کاربری</a>';
            }
            if($pr['payment_status']){
                $status = '<b><font color="green">✔</font></b>';
                $active='-';
            }else{
                $status = '<b><font color="red">✖</font></b>';
                $active='<a href="payments.php?type='.$type_page.'&action=done&pid='.$pr['payment_id'].'">تایید پرداخت</a>';
            }
            echo '<tr>
                    <td>'.$pr['payment_id'].'</td>
                    <td>'.num($pr["payment_amount"],true).' تومان</td>
                    <td>'.num($pr["payment_authority"]).'</td>
                    <td>'.num($pr["payment_refid"]).' </td>
                    <td>'.$pr["payment_date"].' - '.$pr["payment_jdate"].'</td>
                    <td>'.$type.'</td>
                    <td>'.$status.'</td>
                    <td>'.$active.'</td>
                </tr>
            ';
        }
        echo "</table>
               
            ";
        for ($i=0;$i<=$pages;$i++){
            echo '<a class="a" href="payments.php?type='.$type_page.'&page='.$i.'">'.$i.'</a>';
        }
    }
}
include 'includes/footer.php';
?>