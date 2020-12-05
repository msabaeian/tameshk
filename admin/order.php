<?php
include 'includes/header.php';
permission("discount",true);
$type = isset($_GET['type']) ? $_GET['type'] : "all";
if(isset($type)){
    $PHP_SELF = $PHP_SELF."?type=".$type;
    if($type=="all"){
        echo ' 
        <header class="top"> <i class="fa fa-dollar" aria-hidden="true"></i> سفارشات <form action="'.$PHP_SELF.'"><input name="s" placeholder="جستجو"><input type="submit" hidden> </form></header>
</header>
        <table class="tbl">
        <tr class="top">
            <td>کد سفارش</td>
            <td>کاربر</td>
            <td>فروشگاه</td>
            <td>هزینه</td>
            <td>وضعیت</td>
            <td>عملیات</td>
        </tr>
        ';
        // ✔ ✖
        @$s = cleanInput($_GET['s']);
        $sql = (isset($_GET['s'])) ? "SELECT * FROM `orders` as orders
	LEFT JOIN `users` as users ON orders.order_user_id = users.users_ID
    LEFT JOIN `shops` as shops ON orders.order_shop_id = shops.shop_id
	WHERE orders.order_code LIKE '%$s%' OR users.user_display_name LIKE '%$s%' ORDER BY orders.order_id DESC LIMIT 40 " :"SELECT * FROM `orders` as orders
	LEFT JOIN `users` as users ON orders.order_user_id = users.users_ID
    LEFT JOIN `shops` as shops ON orders.order_shop_id = shops.shop_id
	ORDER BY orders.order_id DESC LIMIT 40";
        $result = query($sql);
        foreach ($result as $pr){
            //$active = ($pr['city_active']==0) ? "<b><a style='color: green !important' href='city.php?action=active&cityId=".$pr['city_id']."'>✔</a></b>" : "<b><a style='color: rgb(218, 0, 0) !important' href='city.php?action=unActive&cityId=".$pr['city_id']."'>✖</a></b>"
            if($pr['order_status'] == 0){
                $type = "-";
            }else if($pr['order_status'] == 1){
                $type = "در انتظار پرداخت";
            }else if($pr['order_status'] == 2){
                $type = "در انتظار تایید";
            }else if($pr['order_status'] == 3){
                $type = "سفارش تایید شده – در انتظار ارسال";
            }else if($pr['order_status'] == 4){
                $type = "آماده و ارسال شده است";
            }else if($pr['order_status'] == 5){
                $type = "پرداخت انجام نشد";
            }else if($pr['order_status'] == 5){
                $type = "لغو شده توسط کاربر";
            }else if($pr['order_status'] == 5){
                $type = "لغو شده توسط فروشگاه";
            }
            $do = "<a href='".ADDR."admin/payments.php?type=all&s=".$pr["order_id"]."'>یافتن پرداخت ها</a>";
            echo '<tr>
                    <td>'.$pr["order_code"].'</td>
                    <td><a href="users.php?uid='.$pr["order_user_id"].'">'.$pr["user_display_name"].'</a></td>
                    <td><a href="shopy.php?type=show&shopId='.$pr["order_shop_id"].'">'.$pr["shop_name"].'</a></td>
                    <td>'.num($pr["order_total"],true).' تومان </td>
                    <td>'.$type.'</td>
                    <td>'.$do.'</td>
                </tr>
            ';
        }
        echo "</table>
                <!-- <a href='discount.php?type=add'><div class='btn btn-green'><i class='fa fa-plus'></i>  افزودن</div> </a> -->
            ";
    }else if($type=="done"){
        echo ' 
        <header class="top"> <i class="fa fa-dollar" aria-hidden="true"></i> سفارشات <form action="'.$PHP_SELF.'"><input name="s" placeholder="جستجو"><input type="submit" hidden> </form></header>
        <table class="tbl">
        <tr class="top">
            <td>شماره سفارش</td>
            <td>کاربر</td>
            <td>فروشگاه</td>
            <td>هزینه</td>
            <td>وضعیت</td>
            <td>عملیات</td>
        </tr>
        ';
        // ✔ ✖
        $sql = (isset($_GET['s'])) ? "SELECT * FROM `orders` as orders
	LEFT JOIN `users` as users ON orders.order_user_id = users.users_ID
    LEFT JOIN `shops` as shops ON orders.order_shop_id = shops.shop_id
	WHERE  orders.`order_status` = 4 AND orders.order_code LIKE '%$s%' OR users.user_display_name LIKE '%$s%' ORDER BY orders.order_id DESC LIMIT 40 " :"SELECT * FROM `orders` as orders
	LEFT JOIN `users` as users ON orders.order_user_id = users.users_ID
    LEFT JOIN `shops` as shops ON orders.order_shop_id = shops.shop_id
	WHERE  orders.`order_status` = 4 ORDER BY orders.order_id DESC LIMIT 40 ";

        $result = query($sql);
        foreach ($result as $pr){
            //$active = ($pr['city_active']==0) ? "<b><a style='color: green !important' href='city.php?action=active&cityId=".$pr['city_id']."'>✔</a></b>" : "<b><a style='color: rgb(218, 0, 0) !important' href='city.php?action=unActive&cityId=".$pr['city_id']."'>✖</a></b>"
            if($pr['order_status'] == 0){
                $type = "-";
            }else if($pr['order_status'] == 1){
                $type = "در انتظار پرداخت";
            }else if($pr['order_status'] == 2){
                $type = "در انتظار تایید";
            }else if($pr['order_status'] == 3){
                $type = "سفارش تایید شده – در انتظار ارسال";
            }else if($pr['order_status'] == 4){
                $type = "آماده و ارسال شده است";
            }else if($pr['order_status'] == 5){
                $type = "پرداخت انجام نشد";
            }else if($pr['order_status'] == 5){
                $type = "لغو شده توسط کاربر";
            }else if($pr['order_status'] == 5){
                $type = "لغو شده توسط فروشگاه";
            }
            $do = "<a href='".ADDR."admin/payments.php?type=all&s=".$pr["order_id"]."'>یافتن پرداخت ها</a>";
            echo '<tr>
                    <td>'.$pr["order_code"].'</td>
                    <td><a href="users.php?uid='.$pr["order_user_id"].'">'.$pr["user_display_name"].'</a></td>
                    <td><a href="shopy.php?type=show&shopId='.$pr["order_shop_id"].'">'.$pr["shop_name"].'</a></td>
                    <td>'.num($pr["order_total"],true).' تومان </td>
                    <td>'.$type.'</td>
                    <td>'.$do.'</td>
                </tr>
            ';
        }
        echo "</table>
                <!-- <a href='discount.php?type=add'><div class='btn btn-green'><i class='fa fa-plus'></i>  افزودن</div> </a> -->
            ";
    }else if($type=="undone"){
        echo ' 
        <header class="top"> <i class="fa fa-dollar" aria-hidden="true"></i> سفارشات</header>
        <table class="tbl">
        <tr class="top">
            <td>شماره سفارش</td>
            <td>کاربر</td>
            <td>فروشگاه</td>
            <td>هزینه</td>
            <td>وضعیت</td>
            <td>عملیات</td>
        </tr>
        ';
        // ✔ ✖
        $result = query("SELECT * FROM `orders` as orders
	LEFT JOIN `users` as users ON orders.order_user_id = users.users_ID
    LEFT JOIN `shops` as shops ON orders.order_shop_id = shops.shop_id
    WHERE orders.`order_status` = 0 OR orders.`order_status` = 4
	ORDER BY orders.order_id DESC LIMIT 40 ");
        foreach ($result as $pr){
            //$active = ($pr['city_active']==0) ? "<b><a style='color: green !important' href='city.php?action=active&cityId=".$pr['city_id']."'>✔</a></b>" : "<b><a style='color: rgb(218, 0, 0) !important' href='city.php?action=unActive&cityId=".$pr['city_id']."'>✖</a></b>"
            if($pr['order_status'] == 0){
                $type = "-";
            }else if($pr['order_status'] == 1){
                $type = "در انتظار پرداخت";
            }else if($pr['order_status'] == 2){
                $type = "در انتظار تایید";
            }else if($pr['order_status'] == 3){
                $type = "سفارش تایید شده – در انتظار ارسال";
            }else if($pr['order_status'] == 4){
                $type = "آماده و ارسال شده است";
            }else if($pr['order_status'] == 5){
                $type = "پرداخت انجام نشد";
            }else if($pr['order_status'] == 5){
                $type = "لغو شده توسط کاربر";
            }else if($pr['order_status'] == 5){
                $type = "لغو شده توسط فروشگاه";
            }
            $do = "<a href='".ADDR."admin/payments.php?type=all&s=".$pr["order_id"]."'>یافتن پرداخت ها</a>";
            echo '<tr>
                    <td>'.$pr["order_code"].'</td>
                    <td><a href="users.php?uid='.$pr["order_user_id"].'">'.$pr["user_display_name"].'</a></td>
                    <td><a href="shopy.php?type=show&shopId='.$pr["order_shop_id"].'">'.$pr["shop_name"].'</a></td>
                    <td>'.num($pr["order_total"],true).' تومان </td>
                    <td>'.$type.'</td>
                    <td>'.$do.'</td>
                </tr>
            ';
        }
        echo "</table>
                <!-- <a href='discount.php?type=add'><div class='btn btn-green'><i class='fa fa-plus'></i>  افزودن</div> </a> -->
            ";
    }
}
include 'includes/footer.php';
?>