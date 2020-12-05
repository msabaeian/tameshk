<?php
include 'includes/header.php';
permission("discount",true);
$type = isset($_GET['type']) ? $_GET['type'] : "all";
if(isset($action)){
    if($action == "add" && check_set_empty($discount_description)&& check_set_empty($discount_city,false)&& check_set_empty($discount_shop,false)
        && check_set_empty($discount_uid,false)
        && check_set_empty($discount_percent)
        && check_set_empty($discount_min,false)
        && check_set_empty($discount_delete_date)
        && check_set_empty($discount_code)
        && check_set_empty($discount_total_use,false)
    ){
        global $admin_id;
        $add['discount_percent'] = floatval($discount_percent);
        $add['discount_city'] = intval($discount_city);
        $add['discount_shop'] = intval($discount_shop);
        $add['discount_uid'] = intval($discount_uid);
        $add['discount_min'] = intval($discount_min);
        $add['discount_date'] = get_date();
        $add['discount_delete_date'] = cleanInput($discount_delete_date);
        $add['discount_active'] = 1;
        $add['discount_description'] = cleanInput($discount_description);
        $add['discount_by'] = $admin_id;
        $add['discount_code'] = cleanInput($discount_code);
        $add['discount_total_use'] = intval($discount_total_use);
        query(queryInsert('discounts',$add));
        $add_ok = true;
    }
}
if(isset($type)){
    $PHP_SELF = $PHP_SELF."?type=".$type;
    if($type=="all"){
        echo ' 
        <header class="top"> <i class="fa fa-dollar" aria-hidden="true"></i> تخفیف ها</header>
        <table class="tbl">
        <tr class="top">
            <td>#</td>
            <td>نام</td>
            <td>میزان</td>
            <td>کمترین مقدار خرید</td>
            <td>پایان</td>
            <td>نوع</td>
            <td>عملیات</td>
        </tr>
        ';
        // ✔ ✖
        $result = query("SELECT * FROM `discounts` ORDER  BY `discount_id` DESC");
        $i = 0;
        foreach ($result as $pr){
            $i++;
            //$active = ($pr['city_active']==0) ? "<b><a style='color: green !important' href='city.php?action=active&cityId=".$pr['city_id']."'>✔</a></b>" : "<b><a style='color: rgb(218, 0, 0) !important' href='city.php?action=unActive&cityId=".$pr['city_id']."'>✖</a></b>";
            if (check_set_empty($pr['discount_shop'])){
                $shopId= $pr['discount_shop'];
                $type = "<a target='_blank' href='shopy.php?type=shopId=$shopId'>فروشگاه</a>";
            }else if (check_set_empty($pr['discount_city'])){
                $cityId= $pr['discount_city'];
                $type = "شهر: ".query("SELECT * FROM `cities` WHERE `city_id` = ".$cityId)->fetch()['city_name'];
            }else if (check_set_empty($pr['discount_uid'])){
                $uid= $pr['discount_uid'];
                $type = "<a target='_blank' href='users.php?type=userID=$uid'>کاربر</a>";
            }

            if(intval($pr["discount_percent"]) == -1){
                $pr["discount_percent"] = "پست رایگان";
            }
            echo '<tr>
                    <td>'.$i.'</td>
                    <td>'.$pr["discount_description"].'</td>
                    <td>'.$pr["discount_percent"].'</td>
                    <td>'.num($pr["discount_min"],true).' تومان </td>
                    <td>'.$pr["discount_delete_date"].'</td>
                    <td>'.$type.'</td>
                    <td>عملیات</td>
                </tr>
            ';
        }
        echo "</table>
                <a href='discount.php?type=add'><div class='btn btn-green'><i class='fa fa-plus'></i>  افزودن</div> </a>
            ";
    }else if($type=="add"){
        if(isset($add_ok)){
            ok("با موفقیت افزوده شد");
        }
        
        echo '<header class="top"><i class="fa fa-map-marker" aria-hidden="true"></i> افزودن شهر</header>
              <div class="form">
              <form action="discount.php?type=add&action=add" method="post">
                <label>توضیح تخفیف: <input type="text" placeholder="توضیح تخفیف" name="discount_description"></label><br><br>
                <label>کد تخفیف: <input type="text" placeholder="کد تخفیف" value="'.createCode().'" name="discount_code"></label><br><br>
                <label>مربوط به شهر: <input type="text" placeholder="کد شهر" name="discount_city" value="0"></label><br><br>
                <label>مربوط به فروشگاه: <input type="text" placeholder="کد فروشگاه" name="discount_shop" value="0"></label><br><br>
                <label>مربوط به شخص: <input type="text" placeholder="کد شخص" name="discount_uid" value="0"></label><br><br>
                <label>میزان تخفیف: <input type="text" placeholder="مبلغ تخفیف" name="discount_percent"></label>
                <p>برای پست رایگان مقدار -1 را وارد نمایید</p>
                <br><br>
                <label>تعداد قابل استفاده: <input type="text" placeholder="تعداد" name="discount_total_use"></label>
                <p>برای نامحدود مقدار -1 را وارد نمایید</p>
                <br><br>
                <label>حداقل میزان خرید: <input type="text" placeholder="حداقل میزان خرید تومان" name="discount_min"></label><br><br>
                <label>تاریخ پایان کد: <input type="text" placeholder="2016-10-04" name="discount_delete_date" value="2016-10-04"></label><br><br>
               <input type="submit" value="تایید" style="margin-top: -77px; margin-left: 200px;" class=\'btn btn-blue float-left\'>
              </form>
            </div>
        ';
    }
}
include 'includes/footer.php';
?>