<?php
include 'includes/header.php';
permission("city",true);
$type = isset($_GET['type']) ? $_GET['type'] : "all";
if(isset($action)){
    if($action == "active" && isset($cityId) && !empty($cityId)){
        city_state($cityId);
    }else if($action == "unActive" && isset($cityId) && !empty($cityId)){
        city_state($cityId,false);
    }else if($action == "add" && isset($name) && !empty($name) && isset($state) && !empty($state)){
        if(addCity($name,$state)){
            $especial_name = array();
            global $db;
            $especial_name['city_meta_city_id'] = $db->lastInsertId();
            $especial_name['city_meta_key'] = "especial_title";
            $especial_name['city_meta_value'] = "پیشنهاد ویژه";
            $especial_name['city_meta_date'] = get_date();
            query(queryInsert('cities_meta',$especial_name));
            $add_ok = "";
        }
    }
}
if(isset($type)){
    $PHP_SELF = $PHP_SELF."?type=".$type;
    if($type=="all"){
        echo ' 
        <header class="top"> <i class="fa fa-map-signs" aria-hidden="true"></i> شهر ها</header>
        <table class="tbl">
        <tr class="top">
            <td>#</td>
            <td>نام</td>
            <td>استان</td>
            <td>فعال</td>
        </tr>
        ';
        // ✔ ✖
        $result = query("SELECT * FROM `cities` as cities
        LEFT JOIN `states` as states ON `states`.`state_id` = cities.`city_state`");
        $i = 0;
        foreach ($result as $pr){
            $i++;
            $active = ($pr['city_active']==0) ? "<b><a style='color: green !important' href='city.php?action=active&cityId=".$pr['city_id']."'>✔</a></b>" : "<b><a style='color: rgb(218, 0, 0) !important' href='city.php?action=unActive&cityId=".$pr['city_id']."'>✖</a></b>";
            echo '<tr>
                    <td>'.$i.'</td>
                    <td><a href="city.php?type=show&cid='.$pr["city_id"].'">'.$pr["city_name"].'</a></td>
                    <td>'.$pr["state_name"].'</td>
                    <td>'.$active.'</td>
                </tr>
            ';
        }
        echo "</table>
                <a href='city.php?type=add'><div class='btn btn-green'><i class='fa fa-plus'></i>  افزودن شهر</div> </a>
            ";
    }else if($type=="add"){
        if(isset($add_ok)){
            ok("با موفقیت افزوده شد");
        }
        echo '<header class="top"><i class="fa fa-map-marker" aria-hidden="true"></i> افزودن شهر</header>
              <div class="form">
              <form action="city.php?type=add&action=add" method="post">
                <label>نام شهر: <input type="text" placeholder="نام شهر" name="name"></label><br><br>
                <label>استان: '.states().'</label><br>
               <input type="submit" value="تایید" style="margin-top: -77px; margin-left: 200px;" class=\'btn btn-blue float-left\'>
              </form>
            </div>
        ';
    }else if($type=="show" && check_set_empty($cid)){


        $cid = intval($cid);
        if(isset($add_ok)){
            ok("با موفقیت افزوده شد");
        }
        $city = query("SELECT * FROM `cities` WHERE `city_id` = ".$cid);
        if($city->rowCount()){

            if(check_set_empty(@$mid)){
                $mid = intval($mid);
                $del['city_meta_active'] = 0;
                query(queryUpdate('cities_meta',$del,' WHERE `city_meta_id` = '.$mid));
            }

            if(check_set_empty(@$updateHome)){
                UpdateCityHome($cid);
            }

            if(check_set_empty(@$city_meta_value) && check_set_empty($city_meta_data,false) && check_set_empty($meta_type)){
                $add['city_meta_city_id'] = $cid;
                $add['city_meta_value'] = cleanInput($city_meta_value);
                $add['city_meta_data'] = cleanInput($city_meta_data);
                $add['city_meta_date'] = get_date();
                $add['city_meta_key'] = cleanInput($meta_type);
                query(queryInsert('cities_meta',$add));
                echo queryInsert('cities_meta',$add);
            }

            if(check_set_empty(@$especial_title) && check_set_empty(@$especial_title_mid)){
                $del['city_meta_active'] = 0;
                query(queryUpdate('cities_meta',$del,' WHERE `city_meta_id` = '.intval($especial_title_mid)));

                $especial_name = array();
                $especial_name['city_meta_city_id'] = $cid;
                $especial_name['city_meta_key'] = "especial_title";
                $especial_name['city_meta_value'] = cleanInput($especial_title);
                $especial_name['city_meta_date'] = get_date();
                query(queryInsert('cities_meta',$especial_name));
            }

            $city = $city->fetch();

            $especial_title = $db->query("SELECT `city_meta_value` as especial_title FROM `cities_meta` WHERE `city_meta_key` = 'especial_title' AND `city_meta_active` AND `city_meta_city_id` = ".$cid)->fetch()['especial_title'];
            $especial_title_id = $db->query("SELECT `city_meta_id` as city_meta_id FROM `cities_meta` WHERE `city_meta_key` = 'especial_title' AND `city_meta_active` AND `city_meta_city_id` = ".$cid)->fetch()['city_meta_id'];
            $lastHomeUpdate = $db->query("SELECT `city_meta_date` as city_meta_date FROM `cities_meta` WHERE `city_meta_key` = 'homeData' AND `city_meta_active` AND `city_meta_city_id` = ".$cid)->fetch()['city_meta_date'];
            echo ' 
        <header class="top"> <i class="fa fa-dollar" aria-hidden="true"></i> '.$city['city_name'] .'</header><br><br>
        
        <form action="city.php?type=show&cid='.$cid.'" method="post">
        آخرین بروزرسانی صفحه اصلی:'.$lastHomeUpdate.'
            <input type="text" placeholder="" value="true" name="updateHome" hidden><br><br>
               <input type="submit" value="بروزرسانی اطلاعات صفحه اصلی" style="margin-top: -77px; margin-left: 200px;" class=\'btn btn-red float-left\'>
        </form>
        
        <form action="city.php?type=show&cid='.$cid.'" method="post">
                <label>نام لیست ویژه: <input type="text" placeholder="" value="'.$especial_title.'" name="especial_title"></label><br><br>
                <input type="text" placeholder="" value="'.$especial_title_id.'" name="especial_title_mid" hidden><br><br>
               <input type="submit" value="بروزرسانی" style="margin-top: -77px; margin-left: 200px;" class=\'btn btn-blue float-left\'>
              </form>      
        
        <br><br>
        اسپلش بالا
        <table class="tbl">
        <tr class="top">
            <td>#</td>
            <td>نوع</td>
            <td>مقدار</td>
            <td>عملیات</td>
        </tr>
        ';
            // ✔ ✖
            $result = query("SELECT * FROM `cities_meta` WHERE `city_meta_city_id` = ".$cid." AND `city_meta_active` AND `city_meta_key` = 'top_splash'");
            $i = 0;
            foreach ($result as $pr){
                $i++;
                //$active = ($pr['city_active']==0) ? "<b><a style='color: green !important' href='city.php?action=active&cityId=".$pr['city_id']."'>✔</a></b>" : "<b><a style='color: rgb(218, 0, 0) !important' href='city.php?action=unActive&cityId=".$pr['city_id']."'>✖</a></b>";
                if (startsWith($pr['city_meta_data'],"shop:")){
                    $shopid = substr($pr['city_meta_data'],5,strlen($pr['city_meta_data'])) ;
                    $type = "<a href='shopy.php?type=show&shopId=".$shopid."'>نمایش فروشگاه</a>";
                }else if (startsWith($pr['city_meta_data'],"section:")){
                    $shopid = substr($pr['city_meta_data'],8,strlen($pr['city_meta_data'])) ;
                    $type = "<a href='shopy.php?type=show&sectionId=".$shopid."'>نمایش بخش</a>";
                }else if (startsWith($pr['city_meta_data'],"product:")){
                    $shopid = substr($pr['city_meta_data'],8,strlen($pr['city_meta_data'])) ;
                    $type = "نمایش دسته بندی";
                }else if (startsWith($pr['city_meta_data'],"link:")){
                    $shopid = substr($pr['city_meta_data'],5,strlen($pr['city_meta_data'])) ;
                    $type = "<a href='".$shopid."'>باز کردن لینک خاص</a>";
                }else if (startsWith($pr['city_meta_data'],"news:")){
                    $shopid = substr($pr['city_meta_data'],5,strlen($pr['city_meta_data'])) ;
                    $type = "<a href='news.php?nid=".$shopid."'>باز کردن خبر</a>";
                }else {
                    $type = "نمایشی";
                }
                echo '<tr>
                    <td>'.$i.'</td>
                    <td>'.$type.'</td>
                    <td>'.$pr["city_meta_value"].'</td>
                    <td><a style="color:red !important;" href="city.php?type=show&cid='.$cid.'&mid='.$pr['city_meta_id'].'">حذف</a></td>
                </tr>
            ';
            }
            echo "</table>
                <a href='city.php?type=add_top_splash&cid=".$cid."'><div class='btn btn-green'><i class='fa fa-plus'></i>  افزودن</div> </a>
            ";

            echo ' 
        <br>
        <br>
        <br>
        اسپلش پایین
        <table class="tbl">
        <tr class="top">
            <td>#</td>
            <td>مقدار</td>
            <td>عملیات</td>
        </tr>
        ';
            // ✔ ✖
            $result = query("SELECT * FROM `cities_meta` WHERE `city_meta_city_id` = ".$cid." AND `city_meta_active` AND `city_meta_key` = 'bottom_splash'");
            $i = 0;
            foreach ($result as $pr){
                $i++;
                //$active = ($pr['city_active']==0) ? "<b><a style='color: green !important' href='city.php?action=active&cityId=".$pr['city_id']."'>✔</a></b>" : "<b><a style='color: rgb(218, 0, 0) !important' href='city.php?action=unActive&cityId=".$pr['city_id']."'>✖</a></b>";
                if (startsWith($pr['city_meta_data'],"shop:")){
                    $shopid = substr($pr['city_meta_data'],5,strlen($pr['city_meta_data'])) ;
                    $type = "<a href='shopy.php?type=show&shopId=".$shopid."'>نمایش فروشگاه</a>";
                }else if (startsWith($pr['city_meta_data'],"section:")){
                    $shopid = substr($pr['city_meta_data'],8,strlen($pr['city_meta_data'])) ;
                    $type = "<a href='shopy.php?type=show&sectionId=".$shopid."'>نمایش بخش</a>";
                }else if (startsWith($pr['city_meta_data'],"product:")){
                    $shopid = substr($pr['city_meta_data'],9,strlen($pr['city_meta_data'])) ;
                    $type = "نمایش دسته بندی";
                }else if (startsWith($pr['city_meta_data'],"link:")){
                    $shopid = substr($pr['city_meta_data'],5,strlen($pr['city_meta_data'])) ;
                    $type = "<a href='".$shopid."'>باز کردن لینک خاص</a>";
                }else if (startsWith($pr['city_meta_data'],"news:")){
                    $shopid = substr($pr['city_meta_data'],5,strlen($pr['city_meta_data'])) ;
                    $type = "<a href='news.php?nid=".$shopid."'>باز کردن خبر</a>";
                }else {
                    $type = "نمایشی";
                }
                echo '<tr>
                    <td>'.$i.'</td>
                    <td>'.$type.'</td>
                    <td>'.$pr["city_meta_value"].'</td>
                    <td><a style="color:red !important;" href="city.php?type=show&cid='.$cid.'&mid='.$pr['city_meta_id'].'">حذف</a></td>
                </tr>
            ';
            }
            echo "</table>
                <a href='city.php?type=add_bottom_splash&cid=".$cid."'><div class='btn btn-green'><i class='fa fa-plus'></i>  افزودن</div> </a>
            ";

        }else{
            error("شهر مورد نظر یافت نشد");
        }

    }else if($type=="add_top_splash" && check_set_empty($cid)){
        echo '<header class="top"><i class="fa fa-map-marker" aria-hidden="true"></i> افزودن اسپلش بالا</header>
              <div class="form">
              <form action="city.php?type=show&cid='.$cid.'" method="post">
                <label>آدرس تصویر: <input type="text" placeholder="آدرس تصویر" name="city_meta_value"></label><br><br>
                <label>عملیات: <input type="text" placeholder="عملیات" name="city_meta_data"></label><br><br>
               <input type="text" value="top_splash" name="meta_type" hidden>
                <p>توجه: در صورتی که میخواهید با کلیک بر روی اسپلش فروشگاهی باز شود می بایست عملیات را بصورت shop:10 وارد کنید که عدد 10 کد فروشگاه می باشد</p>
               <p>توجه: در صورتی که میخواهید با کلیک بر روی اسپلش بخشی باز شود می بایست عملیات را بصورت section:10 وارد کنید که عدد 10 کد بخش می باشد</p>
               <p>توجه: در صورتی که میخواهید با کلیک بر روی اسپلش محصول خاصی باز شود می بایست عملیات را بصورت product:10 وارد کنید که عدد 10 کد محصول می باشد</p>
               <p>توجه: در صورتی که میخواهید با کلیک بر روی اسپلش لینک خبری درون برنامه باز شود می بایست عملیات را بصورت news:10 وارد کنید که عدد 10 کد خبر می باشد</p>
               <p>توجه: در صورتی که میخواهید با کلیک بر روی اسپلش آدرس لینک خارجی باز شود می بایست عملیات را بصورت link:link.com وارد کنید که link.com آدرس مورد نظر می باشد</p>
               <br><br><br><br>
               <input type="submit" value="تایید" style="margin-top: -77px; margin-left: 200px;" class=\'btn btn-blue float-left\'>
              </form>
            </div>
        ';
    }else if($type=="add_bottom_splash" && check_set_empty($cid)){
        echo '<header class="top"><i class="fa fa-map-marker" aria-hidden="true"></i> افزودن اسپلش پایین</header>
              <div class="form">
              <form action="city.php?type=show&cid='.$cid.'" method="post">
                <label>آدرس تصویر: <input type="text" placeholder="آدرس تصویر" name="city_meta_value"></label><br><br>
                <label>عملیات: <input type="text" placeholder="کد محصول بخش" name="city_meta_data"></label><br><br>
               <input type="text" value="bottom_splash" name="meta_type" hidden>
                              <p>توجه: در صورتی که میخواهید با کلیک بر روی اسپلش فروشگاهی باز شود می بایست عملیات را بصورت shop:10 وارد کنید که عدد 10 کد فروشگاه می باشد</p>
               <p>توجه: در صورتی که میخواهید با کلیک بر روی اسپلش بخشی باز شود می بایست عملیات را بصورت section:10 وارد کنید که عدد 10 کد بخش می باشد</p>
               <p>توجه: در صورتی که میخواهید با کلیک بر روی اسپلش محصول خاصی باز شود می بایست عملیات را بصورت product:10 وارد کنید که عدد 10 کد محصول می باشد</p>
               <p>توجه: در صورتی که میخواهید با کلیک بر روی اسپلش لینک خبری درون برنامه باز شود می بایست عملیات را بصورت news:10 وارد کنید که عدد 10 کد خبر می باشد</p>
               <p>توجه: در صورتی که میخواهید با کلیک بر روی اسپلش آدرس لینک خارجی باز شود می بایست عملیات را بصورت link:link.com وارد کنید که link.com آدرس مورد نظر می باشد</p>
               <br><br><br><br>
               <input type="submit" value="تایید" style="margin-top: -77px; margin-left: 200px;" class=\'btn btn-blue float-left\'>
              </form>
            </div>
        ';
    }
}
include 'includes/footer.php';
?>