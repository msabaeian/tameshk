<?php
include 'includes/header.php';
permission("shopy",true);

$type = isset($_GET['type']) ? $_GET['type'] : "all";
if(isset($action)){
    if($action == "unBlock" && isset($userId) && !empty($userId)){
        user_lock($userId,false);
    }else if($action == "add" && isset($name) && !empty($name)&& isset($state) && !empty($state)){
        if(addCity($name,$state)){
            $add_ok = "";
        }
    }else if($action == "activeShop" && isset($shopId) && !empty($shopId)){
        shop_do("active",$shopId);
    }else if($action == "addImage" && isset($shopId) && !empty($shopId)){
        require_once('../module/php/uploadImage.php');
        if(isset($name)){
            shop_do("addImage",$shopId,$name);
        }
    }else if($action == "updateLogo" && isset($shopId) && !empty($shopId)){
        require_once('../module/php/uploadImage.php');
        if(isset($name)){
            shop_do("logo",$shopId,$name);
        }
    }else if($action == "updateLogo" && isset($sectionId) && !empty($sectionId)){
        require_once('../module/php/uploadImage.php');
        if(isset($name)){
            section_do("logo",$sectionId,$name);
        }
    }else if($action == "closeShop" && isset($shopId) && !empty($shopId)){
        shop_do("unActive",$shopId);
    }else if($action == "addShop" && isset($name) && !empty($name)&& isset($city) && !empty($city)&& isset($section)&& !empty($percent)&& isset($percent) && !empty($section)&& isset($user) && !empty($user)&& isset($address) && !empty($address)&& isset($telephone) && !empty($telephone) && check_set_empty($shop_category) && check_set_empty($shop_min)){

        if(addShop($name,$city,$section,$user,$address,$telephone,$percent,$shop_category,$shop_min)){
            $addShop = "";
        }else{
            $notAddShop = "";
        }
    }else if($action == "addProduct" && isset($name) && !empty($name)&& isset($plug) && !empty($plug)&& isset($price) && !empty($price)&& isset($shopId) && !empty($shopId)&& isset($product_off)&&  isset($time) && !empty($time)&& isset($time_radio) && !empty($time_radio)){
        ;
        $product_send_type = 0;
        $product_send_type = @$product_send_type_post+@$product_send_type_place;
        $p_name = $name;
        require_once('../module/php/multiUploadImage.php');

        if(addProduct($p_name,$plug,$shopId,$price,$product_off,$product_send_type,$time,$time_radio)){
            $addProduct = "";

            global $db,$fileNames;
            $last = $db->lastInsertId();
            $add_im = array();
            foreach ($fileNames as $image){
                $add_im['product_id'] = $last;
                $add_im['product_meta_key'] = 'image';
                $add_im['product_meta_value'] = $image;
                query(queryInsert('products_meta',$add_im));
            }

            foreach ($_POST['category'] as $cat){
                product_do("addCategory",$last,cleanInput($cat),$shopId);
            }
        }else{
            $notAddProduct = "";
        }
    }else if($type =="editShop" && $action == "update" && isset($shopId) && !empty($shopId)&& isset($name) && !empty($name)&& isset($city) && !empty($city)&& isset($section)&& !empty($percent)&& isset($percent) && !empty($section)&& isset($user) && !empty($user)&& isset($address) && !empty($address)&& isset($telephone) && !empty($telephone)&& isset($delivery_cost) && isset($delivery_free) && check_set_empty($add_cost_km,false) && check_set_empty($delivery_add_cost_price,false) && check_set_empty($shop_lat_lng,false)&& check_set_empty($shop_min,false) && check_set_empty($shop_category)){
        shop_do("updateName",$shopId,$name);
        shop_do("updateCity",$shopId,$city);
        shop_do("updateSection",$shopId,$section);
        shop_do("updatePercent",$shopId,$percent);
        shop_do("updateAdmin",$shopId,$user);
        shop_do("updateAddress",$shopId,$address);
        shop_do("updateTelephone",$shopId,$telephone);
        shop_do("updateDeliveryCost",$shopId,$delivery_cost);
        shop_do("updateDeliveryFree",$shopId,$delivery_free);
        shop_do("updateAddCostKm",$shopId,$add_cost_km);
        shop_do("delivery_add_cost_price",$shopId,$delivery_add_cost_price);
        shop_do("shop_lat_lng",$shopId,$shop_lat_lng);
        shop_do("shop_min",$shopId,$shop_min);
        shop_do("shop_category",$shopId,$shop_category);
        unset($address,$user,$percent,$section,$city,$name,$add_cost_km,$delivery_free,$delivery_add_cost_price,$add_cost_km,$shop_lat_lng,$shop_min,$shop_category);
        $update_shop = '';
    }
}
if(isset($type)){
    $PHP_SELF = $PHP_SELF."?type=".$type;
    if($type=="sections"){ // Show Sections
        @$s = cleanInput($_GET['s']);
        $sql = (isset($_GET['s'])) ? "SELECT * FROM `sections` WHERE `delete` = 0 AND `section_name` LIKE '%$s%'" :"SELECT * FROM `sections` WHERE `delete` = 0";
        echo ' 
        <header class="top"> <i class="fa fa-users" aria-hidden="true"></i> بخش ها <form action="'.$PHP_SELF.'"><input name="s" placeholder="جستجو"><input name="type" value="sections" hidden><input type="submit" hidden> </form></header>
        <table class="tbl">
        <tr class="top">
            <td>#</td>
            <td>نام</td>
            <td>در شهر های</td>
            <td style="width: 150px;">عمل</td>
        </tr>
        ';
        // ✔ ✖
        $result = query($sql);
        $i = 0;
        foreach ($result as $pr){
            $i++;
            $cities_print = "";
            $info = query("SELECT * FROM `sections_meta` WHERE `section_meta_key` = 'city' AND `section_meta_section_id`= ".$pr['section_id']);
            foreach ($info as $print){
                $city = query("SELECT * FROM `cities` WHERE `city_id` = ".$print['section_meta_value']);
                $city = $city->fetch();
                if($city['city_active']){
                    $cities_print = $cities_print.'<a style="color:green !important" href="city.php?type=show&cityId='.$print['section_meta_value'].'">'.$city['city_name'].'</a> ، ';
                }else{
                    $cities_print = $cities_print.'<a style="color:rgb(63,81,181) !important" href="city.php?type=show&cityId='.$print['section_meta_value'].'">'.$city['city_name'].'</a> ، ';
                }
            }
            $cities_print = mb_substr($cities_print,0,mb_strlen($cities_print)-3);
            $active = ($pr['active']) ? '<span class="color-red pointer unActiveSection" sectionId="'.$pr['section_id'].'" title="غیر فعال سازی">غیر فعال سازی</span>' : '<span class="color-green pointer activeSection" sectionId="'.$pr['section_id'].'" title="فعال سازی">فعال سازی</span>' ;
            echo '<tr>
                    <td>'.$i.'</td>
                    <td><a href="shopy.php?type=show&sectionId='.$pr['section_id'].'">'.$pr["section_name"].'</a></td>
                    <td>'.$cities_print.'</td>
                    <td>
                        '.$active.'<br>
                        <a style="color:rgb(0,150,136) !important" href="shopy.php?type=show&sectionId='.$pr['section_id'].'">نمایش/ویرایش</a>
                        <br>
                        <a class="deleteSection pointer" sectionId="'.$pr['section_id'].'" style="color:red !important" >حذف</a>
                        
                    </td>
                </tr>
            ';
        }
        echo "</table>
                <!-- <a href='users.php?type=add'><div class='btn btn-green'><i class='fa fa-user-plus'></i>  افزودن کاربر</div> </a> -->
                <div class=\"btn btn-green addSection\" style=\"float:none; width: 179px;\"><input placeholder='نام' class='sectionName'><br>افزودن بخش<i class=\"fa fa-plus\" aria-hidden=\"true\"></i> </div>
            ";
    }else if($type=="show" && isset($sectionId) && !empty($sectionId)) { // 'show section'
        $section = query("SELECT * FROM `sections` WHERE `section_id` = ".intval($sectionId));
        if($section->rowCount() >= 1){
            $section = $section->fetch();
            
            
            // Cities
            echo '<div class="box float-right"><header><i class="fa fa-map-marker" aria-hidden="true"></i> شهر ها</header>
                    <table class="tbl no-border-right no-border-left no-border-top"><input hidden class="sectionId" value="'.$sectionId.'">';
                    foreach (cities($sectionId) as $pr){
                        echo '
                            <tr>
                                <td>'.$pr.'</td>
                                <td class="color-red pointer deleteCity">حذف</td>
                            </tr>
                        ';
                    }
            $cities = query("SELECT * FROM `cities` ");
            $select = "<select>";
            foreach ($cities as $city){
                $select = $select.'<option value="'.$city['city_id'].'">'.$city['city_name'].'</option>';
            }
            $select = $select."</select>";
                  echo '</table><br><div>'.$select.'&nbsp;<div class="btn btn-green addCityToSection" style="width:80px; float:none;">افزودن<i class="fa fa-plus" aria-hidden="true"></i></div></div>
                  
                  </div>';
            // Cities
            unset($select,$cities);
            // Category
            echo '<div class="box float-right margin-right-25"><header><i class="fa fa-plus-square-o" aria-hidden="true"></i> دسته بندی محصولات بخش</header>
                    <table class="tbl no-border-right no-border-left no-border-top"><input hidden class="sectionId" value="'.$sectionId.'">';
            foreach (query("SELECT * FROM `sections_meta` WHERE `section_meta_key` = 'category' AND `section_meta_section_id` = '$sectionId'") as $pr){
                $active = ($pr['meta_active']) ? '<td class="color-red pointer unActiveCategory" categoryId="'.$pr['section_meta_id'].'" title="غیر فعال سازی">غیر فعال سازی</td>' : '<td class="color-green pointer activeCategory" categoryId="'.$pr['section_meta_id'].'" title="فعال سازی">فعال سازی</td>' ;
                echo '
                            <tr>
                                <td>'.$pr['section_meta_id'].'</td>
                                <td>'.$pr['section_meta_value'].'</td>
                                '.$active.'
                            </tr>
                        ';
            }
            unset($active);
            echo '</table><br><div><div class="btn btn-green addCategory" style="float:none; width: 179px;"><input placeholder=\'نام\' class=\'categoryName\'><br>افزودن دسته<i class="fa fa-plus" aria-hidden="true"></i> </div></div>
                  
                  </div>';
            // Category

            // Category
            echo '<div class="box float-right margin-right-25"><header><i class="fa fa-plus-square-o" aria-hidden="true"></i> دسته بندی فروشگاه ها</header>
                    <table class="tbl no-border-right no-border-left no-border-top"><input hidden class="sectionId" value="'.$sectionId.'">';
            foreach (query("SELECT * FROM `sections_meta` WHERE `section_meta_key` = 'shop_category' AND `section_meta_section_id` = '$sectionId'") as $pr){
                $active = ($pr['meta_active']) ? '<td class="color-red pointer unActiveShopCategory" categoryId="'.$pr['section_meta_id'].'" title="غیر فعال سازی">غیر فعال سازی</td>' : '<td class="color-green pointer activeShopCategory" categoryId="'.$pr['section_meta_id'].'" title="فعال سازی">فعال سازی</td>' ;
                echo '
                            <tr>
                                <td>'.$pr['section_meta_id'].'</td>
                                <td>'.$pr['section_meta_value'].'</td>
                                '.$active.'
                            </tr>
                        ';
            }
            unset($active);
            echo '</table><br><div><div class="btn btn-green addShopCategory" style="float:none; width: 179px;"><input placeholder=\'نام\' class=\'categoryNameShop\'><br>افزودن دسته<i class="fa fa-plus" aria-hidden="true"></i> </div></div>
                  
                  </div>';
            // Category

            // Logo
            $logo = query("SELECT `section_meta_value` as banner FROM `sections_meta` WHERE `section_meta_section_id` = '".$sectionId."' AND `section_meta_key` = 'banner' AND `meta_active`")->fetch()['banner'];
            echo '<div class="box float-right margin-right-25" style="max-height: 300px; overflow-y: scroll; overflow-x: hidden"><header><i class="fa fa-file-image-o" aria-hidden="true"></i> لوگو</header>
                    <table  class="tbl no-border-right no-border-left no-border-top"><input hidden class="sectionId" value="'.$sectionId.'">
                    <img src="'.UPLOADS.$logo.'" style="width: 100%;">
                    
                    ';

            echo '<br><form enctype="multipart/form-data" action="shopy.php?type=show&sectionId='.$sectionId.'&action=updateLogo" method="post"><input placeholder=\'نام\' class=\'shop_add_image\' type="file" name="file"><div><input type="submit" class="btn btn-green addShopImage" style="float:none; width: 179px;" value="بروزرسانی لوگو"></form></div><br></div><br>
                  
                  </div>';

            // Logo
        }else{
            not_found("بخش مورد نظر یافت نشد! ");
        }

    }else if($type=="show" && isset($shopId) && !empty($shopId)) { // show shop
        $shop = query("SELECT * FROM `shops` as shops LEFT JOIN `sections` as sections ON shops.`shop_section_id` = sections.`section_id` LEFT JOIN `cities` as cities ON shops.`shop_city_id` = cities.`city_id` LEFT JOIN `users` as users ON users.`users_ID` = shops.`shop_admin` WHERE sections.`delete` = 0 AND `shop_id` = ".intval($shopId));
        if($shop->rowCount() >= 1){
            $shop = $shop->fetch();
$shopId = intval($shopId);

            // Information
            echo '<div class="box float-right"><header><i class="fa fa-info" aria-hidden="true"></i> اطلاعات</header>
                    <table class="tbl no-border-right no-border-left no-border-top"><input hidden class="shopId" value="'.$shopId.'"><br>
                    <h5><i class="fa fa-star" style="color:red" aria-hidden="true"></i> '.$shop['shop_name'].' <font color="green">با مدیریت</font>  <a style="color: rgb(0, 0, 0);" href="users.php?type=show&userID='.$shop['users_ID'].'">'.$shop['user_display_name'].'</a> </h5>
                    <h6><i class="fa fa-map" style="color:red" aria-hidden="true"></i> '.$shop['shop_address'].'</h6>
                    <h6><i class="fa fa-percent" style="color:red" aria-hidden="true"></i> '.$shop['percent'].' درصد </h6>
                    <h6><i class="fa fa-truck" style="color:red" aria-hidden="true"></i> '.$shop['delivery_cost'].' هزینه پست </h6>
                    <h6><i class="fa fa-motorcycle" style="color:red" aria-hidden="true"></i> '.$shop['delivery_free'].' پست رایگان </h6>
                    <p style="direction: ltr; text-align: right; font-size: 10pt;">'.$shop['shop_telephone'].' <i class="fa fa-phone" style="color:red; direction:ltr;" aria-hidden="true"></i> </p>
                    ';
            if(!$shop['shop_active']){
                $open_close_shop = '<a href="shopy.php?type=show&shopId='.$shopId.'&action=activeShop"><div class="btn btn-yellow edit-shop-info" style="width:100px; float:none;">فعال سازی<i class="fa fa-edit" aria-hidden="true"></i></div></a>';
            }else{
                $open_close_shop = '<a href="shopy.php?type=show&shopId='.$shopId.'&action=closeShop"> <div class="btn btn-red edit-shop-info" style="width:130px; float:none;">بستن فروشگاه<i class="fa fa-edit" aria-hidden="true"></i></a>';
            }
                  echo '</table><br><div>&nbsp;<a href="shopy.php?type=editShop&shopId='.$shopId.'"> <div class="btn btn-green edit-shop-info" style="width:80px; float:none;">ویرایش<i class="fa fa-edit" aria-hidden="true"></i></div></a>&nbsp;'.$open_close_shop.'</div><br>
                  <a href="shopy.php?type=addProduct&shopId='.$shopId.'"><div class="btn btn-blue edit-shop-info" style=" float:none;">افزودن محصول<i class="fa fa-plus" aria-hidden="true"></i></div></a><br><br>
                  <a href="shopy.php?type=products&shopId='.$shopId.'"><div class="btn btn-yellow edit-shop-info" style=" float:none;">نمایش محصولات<i class="fa fa-list-alt" aria-hidden="true"></i></div></a>
                  </div>
                  
                  </div>';
            // Information

            // Image
            echo '<div class="box float-right margin-right-25" style="max-height: 300px; overflow-y: scroll; overflow-x: hidden"><header><i class="fa fa-image" aria-hidden="true"></i> تصاویر فروشگاه</header>
                    <table  class="tbl no-border-right no-border-left no-border-top"><input hidden class="sectionId" value="'.$shopId.'">';
            foreach (query("SELECT * FROM `shops_meta` WHERE `shop_meta_key` = 'image' AND `shop_meta_shop_id` = '$shopId' AND `shop_meta_active` != 0") as $pr){
                $active = ($pr['shop_meta_active']) ? '<td class="color-red pointer deleteShopImage" shopImageId="'.$pr['shop_meta_id'].'" title="حذف">حذف</td>' : '' ;
                echo '
                            <tr>
                                <td><img src="'.UPLOADS.$pr['shop_meta_value'].'" width="120px" height="100px"></td>
                                '.$active.'
                            </tr>
                        ';
            }
            echo '</table><br><form enctype="multipart/form-data" action="shopy.php?type=show&shopId='.$shopId.'&action=addImage" method="post"><input placeholder=\'نام\' class=\'shop_add_image\' type="file" name="file"><div><div class="btn btn-green addShopImage" style="float:none; width: 179px;">افزودن تصویر<i class="fa fa-plus" aria-hidden="true"></i><br> </form></div><br></div><br>
                  
                  </div>';
            // Image

            // Logo
            echo '<div class="box float-right margin-right-25" style="max-height: 300px; overflow-y: scroll; overflow-x: hidden"><header><i class="fa fa-file-image-o" aria-hidden="true"></i> لوگو</header>
                    <table  class="tbl no-border-right no-border-left no-border-top"><input hidden class="sectionId" value="'.$shopId.'">';
            foreach (query("SELECT * FROM `shops_meta` WHERE `shop_meta_key` = 'logo' AND `shop_meta_shop_id` = '$shopId' AND `shop_meta_active` != 0") as $pr){
                $active = ($pr['shop_meta_active']) ? '<td class="color-red pointer deleteShopImage" shopImageId="'.$pr['shop_meta_id'].'" title="حذف">حذف</td>' : '' ;
                echo '
                            <tr>
                                <td><img src="'.UPLOADS.$pr['shop_meta_value'].'" width="120px" height="100px"></td>
                                '.$active.'
                            </tr>
                        ';
            }
            echo '</table><br><form enctype="multipart/form-data" action="shopy.php?type=show&shopId='.$shopId.'&action=updateLogo" method="post"><input placeholder=\'نام\' class=\'shop_add_image\' type="file" name="file"><div><div class="btn btn-green addShopImage" style="float:none; width: 179px;">بروزرسانی لوگو<i class="fa fa-plus" aria-hidden="true"></i><br> </form></div><br></div><br>
                  
                  </div>';

            // Cash outs
            $price = query("SELECT SUM(`order_total`) as total , SUM(`order_delivery`) as delivery FROM `orders` WHERE `order_status` = 4 AND `order_shop_id` = ".$shopId); $price = $price->fetch();
            $cash_outs = query("SELECT SUM(`shop_meta_value`) AS cash_out FROM `shops_meta` WHERE `shop_meta_key` = 'cash_out_product' AND `shop_meta_shop_id` = ".$shopId); $cash_outs = intval($cash_outs->fetch()['cash_out']);
            $cash_out_delivery = query("SELECT SUM(`shop_meta_value`) AS cash_out_delivery FROM `shops_meta` WHERE `shop_meta_key` = 'cash_out_delivery' AND `shop_meta_shop_id` = ".$shopId); $cash_out_delivery = intval($cash_out_delivery->fetch()['cash_out_delivery']);
            $cash_out_profit = query("SELECT SUM(`shop_meta_value`) AS cash_out_profit FROM `shops_meta` WHERE `shop_meta_key` = 'cash_out_profit' AND `shop_meta_shop_id` = ".$shopId); $cash_out_profit = intval($cash_out_profit->fetch()['cash_out_profit']);
            $cash_outs = ($cash_outs == NULL) ? 0 : $cash_outs;
            $cash_out_delivery = ($cash_out_delivery == NULL) ? 0 : $cash_out_delivery;
            $cash_out_profit = ($cash_out_profit == NULL) ? 0 : $cash_out_profit;
            /* echo_json(array(
                "t" => $price['total'] , // کل فروش
                "d" => $price['delivery'] ,  // کل هزینه ارسالات
                "cd" => $cash_out_delivery , // تسویه شده ارسالات
                "cp" => $cash_out_profit , // سود
                "c" => $cash_outs , // تسویه شده محصولات
                "b" => $price['total'] - ($price['delivery']+$cash_outs+$cash_out_profit) , // موجودی حاضر
                "bd" => $price['delivery'] - $cash_out_delivery // موجودی حاضر ارسالات
            )); */
            $b= (($price['total'] - ($price['delivery']+$cash_outs+$cash_out_profit))*(100-$shop['percent']))/100;
            echo '</div><div style="margin: 20px;" class="box float-right"><header><i class="fa fa-info" aria-hidden="true"></i> حساب داری</header>
                    <table class="tbl no-border-right no-border-left no-border-top"><input hidden class="shopId" value="'.$shopId.'"><br>
                    <h5><i class="fa fa-star" style="color:red" aria-hidden="true"></i><font color="green"> '.num($price['total'],1).' تومان</font> درآمد کلی فروشگاه</h5>
                    <h5><i class="fa fa-star" style="color:red" aria-hidden="true"></i><font color="green"> '.num($price['delivery'],1).' تومان</font> هزینه ارسال سفارشات</h5>
                    <br><hr><br>
                    <h5><i class="fa fa-star" style="color:red" aria-hidden="true"></i><font color="green"> '.num($cash_outs,1).' تومان</font> تسویه شده محصولات</h5>
                    <h5><i class="fa fa-star" style="color:red" aria-hidden="true"></i><font color="green"> '.num($cash_out_delivery,1).' تومان</font> تسویه شده ارسال</h5>
                    <h5><i class="fa fa-star" style="color:red" aria-hidden="true"></i><font color="green"> '.num($cash_out_profit,1).' تومان</font> سود ما</h5>
                    <br><hr><br>
                    <h5><i class="fa fa-star" style="color:red" aria-hidden="true"></i><font color="green"> '.num($price['total'] - ($price['delivery']+$cash_outs+$cash_out_profit),1).' تومان</font> موجودی فعلی فروشگاه</h5>
                    <h6><i class="fa fa-percent" style="color:red" aria-hidden="true"></i> '.$shop['percent'].' درصد </h6>
                    <h5><i class="fa fa-star" style="color:red" aria-hidden="true"></i><font color="green"> '.num($b,1).' تومان</font> مبلغ قابل تسویه سفارشات</h5>
                    <h5><i class="fa fa-star" style="color:red" aria-hidden="true"></i><font color="green"> '.num($price['delivery'] - $cash_out_delivery,1).' تومان</font> مبلغ قابل تسویه ارسال</h5>
                    <br><hr><br>
                    <h5><i class="fa fa-star" style="color:red" aria-hidden="true"></i><font color="green"> '.num($b+( $price['delivery'] - $cash_out_delivery),1).' تومان</font> مجموع تسویه</h5>
                    ';
                  echo '<br><a href="shopy.php?type=cashout&shopId='.$shopId.'"><div class="btn btn-yellow edit-shop-info" style=" float:none;"><i class="fa fa-dollar" aria-hidden="true"></i> تسویه حساب جدید &nbsp;</div></a>
                  </div>
                  
                  </div>';
        }else{
            not_found("فروشگاه مورد نظر یافت نشد!");
        }

    }else if($type=="cashout" && isset($shopId) && !empty($shopId)) { // cash out
        $shop = query("SELECT * FROM `shops` WHERE `shop_id` = ".intval($shopId));
        if($shop->rowCount() >= 1){
            



            $shop = $shop->fetch();
            $shopId = intval($shopId);

        if(check_set_empty(@$cash_out) && check_set_empty(@$cash_out_product,false) && check_set_empty(@$cash_out_delivery,false)&& check_set_empty(@$cash_out_profit,false)){
            $cash_out = cleanInput($cash_out);
            $cash_out_product = intval($cash_out_product);
            $cash_out_delivery = intval($cash_out_delivery);
            $cash_out_profit = intval($cash_out_profit);

            $insert['shop_meta_shop_id'] = $shopId;
            $insert['shop_meta_key'] = 'cash_out';
            $insert['shop_meta_value'] = $cash_out;
            query(queryInsert('shops_meta',$insert));

            $insert['shop_meta_shop_id'] = $shopId;
            $insert['shop_meta_key'] = 'cash_out_product';
            $insert['shop_meta_value'] = $cash_out_product;
            query(queryInsert('shops_meta',$insert));

            $insert['shop_meta_shop_id'] = $shopId;
            $insert['shop_meta_key'] = 'cash_out_delivery';
            $insert['shop_meta_value'] = $cash_out_delivery;
            query(queryInsert('shops_meta',$insert));

            $insert['shop_meta_shop_id'] = $shopId;
            $insert['shop_meta_key'] = 'cash_out_profit';
            $insert['shop_meta_value'] = $cash_out_profit;
            query(queryInsert('shops_meta',$insert));

            ok('با موفقیت افزوده شد');
        }

        // Cash outs
            $price = query("SELECT SUM(`order_total`) as total , SUM(`order_delivery`) as delivery FROM `orders` WHERE `order_status` = 4 AND `order_shop_id` = ".$shopId); $price = $price->fetch();
            $cash_outs = query("SELECT SUM(`shop_meta_value`) AS cash_out FROM `shops_meta` WHERE `shop_meta_key` = 'cash_out_product' AND `shop_meta_shop_id` = ".$shopId); $cash_outs = intval($cash_outs->fetch()['cash_out']);
            $cash_out_delivery = query("SELECT SUM(`shop_meta_value`) AS cash_out_delivery FROM `shops_meta` WHERE `shop_meta_key` = 'cash_out_delivery' AND `shop_meta_shop_id` = ".$shopId); $cash_out_delivery = intval($cash_out_delivery->fetch()['cash_out_delivery']);
            $cash_out_profit = query("SELECT SUM(`shop_meta_value`) AS cash_out_profit FROM `shops_meta` WHERE `shop_meta_key` = 'cash_out_profit' AND `shop_meta_shop_id` = ".$shopId); $cash_out_profit = intval($cash_out_profit->fetch()['cash_out_profit']);
            $cash_outs = ($cash_outs == NULL) ? 0 : $cash_outs;
            $cash_out_delivery = ($cash_out_delivery == NULL) ? 0 : $cash_out_delivery;
            $cash_out_profit = ($cash_out_profit == NULL) ? 0 : $cash_out_profit;
            $b= (($price['total'] - ($price['delivery']+$cash_outs+$cash_out_profit))*(100-$shop['percent']))/100;
            $pro= ($price['total'] - ($price['delivery']+$cash_outs+$cash_out_profit))-$b;

        echo '<br><a href="shopy.php?type=show&shopId='.$shopId.'"><div class="btn btn-blue edit-shop-info" style=" float:none;"><i class="fa fa-return" aria-hidden="true"></i> بازگشت به فروشگاه &nbsp;</div></a><div class="box float-right"><header><i class="fa fa-info" aria-hidden="true"></i> افزودن تسویه حساب</header>
                <div class="form">
                    <form action="shopy.php?type=cashout" method="post">
                        <label>آیدی فروشگاه: <input type="text" placeholder="آیدی فروشگاه" name="shopId" value="'.$shopId.'"></label>
                        <label>تسویه محصولات: <input type="text" placeholder="تومان" name="cash_out_product"  value="'.$b.'"></label>
                        <label>تسویه ارسال: <input type="text" placeholder="تومان" name="cash_out_delivery" value="'.($price['delivery'] - $cash_out_delivery).'"></label>
                        <label>سود شرکت: <input type="text" placeholder="تومان" name="cash_out_profit" value="'.$pro.'"></label>
                        <label>اطلاعات تسویه حساب: <textarea type="text" style="max-width: 200px;" placeholder="کد پیگیری - بانک و ..." name="cash_out"></textarea></label>
                        <input type="submit" value="تایید" class=\'btn btn-green float-left\'>
                    </form>
                </div>
            </div>
        ';
            
            echo '<div style="margin: 20px;" class="box float-right"><header><i class="fa fa-info" aria-hidden="true"></i> حساب داری</header>
                        <table class="tbl no-border-right no-border-left no-border-top"><input hidden class="shopId" value="'.$shopId.'"><br>
                        <h5><i class="fa fa-star" style="color:red" aria-hidden="true"></i> فروشگاه '.$shop['shop_name'].' </h5><br>
                        <h5><i class="fa fa-star" style="color:red" aria-hidden="true"></i><font color="green"> '.num($price['total'],1).' تومان</font> درآمد کلی فروشگاه</h5>
                        <h5><i class="fa fa-star" style="color:red" aria-hidden="true"></i><font color="green"> '.num($price['delivery'],1).' تومان</font> هزینه ارسال سفارشات</h5>
                        <br><hr><br>
                        <h5><i class="fa fa-star" style="color:red" aria-hidden="true"></i><font color="green"> '.num($cash_outs,1).' تومان</font> تسویه شده محصولات</h5>
                        <h5><i class="fa fa-star" style="color:red" aria-hidden="true"></i><font color="green"> '.num($cash_out_delivery,1).' تومان</font> تسویه شده ارسال</h5>
                        <h5><i class="fa fa-star" style="color:red" aria-hidden="true"></i><font color="green"> '.num($cash_out_profit,1).' تومان</font> سود ما</h5>
                        <br><hr><br>
                        <h5><i class="fa fa-star" style="color:red" aria-hidden="true"></i><font color="green"> '.num($price['total'] - ($price['delivery']+$cash_outs+$cash_out_profit),1).' تومان</font> موجودی فعلی فروشگاه</h5>
                        <h6><i class="fa fa-percent" style="color:red" aria-hidden="true"></i> '.$shop['percent'].' درصد </h6>
                        <h5><i class="fa fa-star" style="color:red" aria-hidden="true"></i><font color="green"> '.num($b,1).' تومان</font> مبلغ قابل تسویه سفارشات</h5>
                        <h5><i class="fa fa-star" style="color:red" aria-hidden="true"></i><font color="green"> '.num($price['delivery'] - $cash_out_delivery,1).' تومان</font> مبلغ قابل تسویه ارسال</h5>
                        <br><hr><br>
                        <h5><i class="fa fa-star" style="color:red" aria-hidden="true"></i><font color="green"> '.num($b+( $price['delivery'] - $cash_out_delivery),1).' تومان</font> مجموع تسویه</h5>
                        ';
                    echo '
                  </div>
                  </div>
                  ';

        
        }else{
            not_found("فروشگاه مورد نظر یافت نشد!");
        }

    }else if($type=="show" && isset($productId) && !empty($productId)) { // show product
        $productId = intval($productId);
        // Actions
        if(isset($action)){

            if($action=="activeProduct"){
                product_do("activeProduct",$productId);
            }
            if($action=="closeProduct"){
                product_do("closeProduct",$productId);
            }
            if($action=="exist"){
                product_do("exist",$productId);
            }
            if($action=="notExist"){
                product_do("notExist",$productId);
            }
            if($action=="especial"){
                product_do("especial",$productId);
            }
            if($action=="notEspecial"){
                product_do("notEspecial",$productId);
            }

            // add image
            if($action=="addImage"){
                require_once('../module/php/uploadImage.php');
                if(isset($name)){
                    product_do("addImage",$productId,$name);
                }
            }

            // Update time
            if($action=="time"){
                if(isset($time) && !empty($time)&& isset($time_radio) && !empty($time_radio)) {
                    product_do("time",$productId,$time_radio,$time);
                }
                if(isset($product_send_type_post) | isset($product_send_type_place)) {
                    $product_send_type = 0;
                    $product_send_type = @$product_send_type_post + @$product_send_type_place;
                    product_do("send_type",$productId,$product_send_type);
                }

            }
        }

        // Query
        $product = query("SELECT * FROM `products` as products LEFT JOIN `shops` as shops ON shops.`shop_id` = products.`product_shop` WHERE `product_delete` = 0 AND `product_id` = ".intval($productId));
        if($product->rowCount() >= 1){
            $product = $product->fetch();

        // Categories
            $product_categories = "";
            foreach(query("SELECT * FROM `products_meta` as meta LEFT JOIN `sections_meta` as cat ON meta.`product_meta_value` = cat.`section_meta_id` LEFT JOIN `sections` as sections ON cat.`section_meta_section_id` = sections.`section_id` WHERE meta.`product_meta_key` = 'category' AND meta.`product_id` = '$productId' AND meta.`product_meta_active` ") as $cat){
                $product_categories = $product_categories.'<a style="color:black; font-size:9pt;" href="shopy.php?type=show&sectionId='.$cat['section_id'].'" target="_blank">'.$cat['section_name'].' -> '.$cat['section_meta_value'].'</a>&nbsp; <font color="red"> - </font> &nbsp;';
            }

            // Information
            echo '<div class="box float-right"><header><i class="fa fa-info" aria-hidden="true"></i> اطلاعات</header>
                    <table class="tbl no-border-right no-border-left no-border-top"><input hidden class="productId" value="'.$productId.'"><br>
                    <h5><i class="fa fa-star" style="color:red" aria-hidden="true"></i> <font color="#0c6dff">'.$product['product_name'].'</font> <font color="green">از فروشگاه</font>  <a style="color: rgb(0, 0, 0);" href="shopy.php?type=show&shopId='.$product['shop_id'].'">'.$product['shop_name'].'</a> </h5>
                    <h6><i class="fa fa-dollar" style="color:red" aria-hidden="true"></i> قیمت:  '.num($product['product_price'],true).' تومان </h6>
                    <h6><i class="fa fa-percent" style="color:red" aria-hidden="true"></i> قیمت تخفیف: '.num($product['product_off'],true).' تومان </h6>
                    
                    <br>
                    <h6><i class="fa fa-plus-square-o" style="color:red" aria-hidden="true"></i> دسته ها:  '.$product_categories.'</h6>
                    <br>توضیحات <h6 style="max-height: 100px; overflow-y: scroll; padding: 8px; text-align: justify; border: 1px solid rgb(207, 207, 207); border-radius: 10px; margin-top: 10px;"><i class="fa fa-text" style="color:red" aria-hidden="true"></i> '.$product['product_description'].' </h6>
                    ';
            if(!$product['product_active']){
                $open_close_product = '<a href="shopy.php?type=show&productId='.$productId.'&action=activeProduct"><div class="btn btn-yellow edit-shop-info" style="width:100px; float:none;">فعال سازی<i class="fa fa-edit" aria-hidden="true"></i></div></a>';
            }else{
                $open_close_product = '<a href="shopy.php?type=show&productId='.$productId.'&action=closeProduct"> <div class="btn btn-red edit-shop-info" style="width:130px; float:none;">بستن محصول<i class="fa fa-edit" aria-hidden="true"></i></div></a>';
            }

            if(!$product['product_exist']){
                $product_exist = '<a href="shopy.php?type=show&productId='.$productId.'&action=exist"><div class="btn btn-blue edit-shop-info" style="width:180px; float:none;"> فعال سازی موجودی <i class="fa  fa-check-square" aria-hidden="true"></i></div></a>';
            }else {
                $product_exist = '<a href="shopy.php?type=show&productId=' . $productId . '&action=notExist"> <div class="btn btn-red edit-shop-info" style="width:180px; float:none;"> غیر فعال سازی موجودی <i class="fa fa-close" aria-hidden="true"></i></div></a>';
            }

            if(!$product['product_especial']){
                $product_especial = '<a href="shopy.php?type=show&productId='.$productId.'&action=especial"><div class="btn btn-blue edit-shop-info" style="width:200px; float:none;margin-top:10px;"> فعال سازی محصول ویژه <i class="fa  fa-check-square" aria-hidden="true"></i></div></a>';
            }else{
                $product_especial = '<a href="shopy.php?type=show&productId='.$productId.'&action=notEspecial"> <div class="btn btn-red edit-shop-info" style="width:200px; float:none;margin-top:10px;"> غیر فعال سازی محصول ویژه <i class="fa fa-close" aria-hidden="true"></i></div></a>';
            }
                  echo '</table><br><div>&nbsp;<a href="shopy.php?type=editProduct&productId='.$productId.'"> <div class="btn btn-green edit-shop-info" style="width:80px; float:none;">ویرایش<i class="fa fa-edit" aria-hidden="true"></i></div></a>&nbsp;'.$open_close_product.'</div><br>
                  '.$product_exist.'<br>'.$product_especial.'
                  </div>
                  
                  </div>';
            // Information

            // Image
            echo '<div class="box float-right margin-right-25" style="max-height: 300px; overflow-y: scroll; overflow-x: hidden"><header><i class="fa fa-image" aria-hidden="true"></i> تصاویر محصول</header>
                    <table  class="tbl no-border-right no-border-left no-border-top"><input hidden class="productId" value="'.$productId.'">';
            foreach (query("SELECT * FROM `products_meta` WHERE `product_meta_key` = 'image' AND `product_id` = '$productId' AND `product_meta_active` != 0") as $pr){
                $active = ($pr['product_meta_active']) ? '<td class="color-red pointer deleteProductImage" productImageId="'.$pr['product_meta_id'].'" title="حذف">حذف</td>' : '' ;
                echo '
                            <tr>
                                <td><img src="'.UPLOADS.$pr['product_meta_value'].'" width="120px" height="100px"></td>
                                '.$active.'
                            </tr>
                        ';
            }
            echo '</table><br><form enctype="multipart/form-data" action="shopy.php?type=show&productId='.$productId.'&action=addImage" method="post"><input class=\'shop_add_image\' type="file" name="file"><div><div class="btn btn-green addShopImage" style="float:none; width: 179px;">افزودن تصویر<i class="fa fa-plus" aria-hidden="true"></i><br> </form></div><br></div><br>
                  
                  </div>';
            // Image

            $product_send_type = (intval($product['product_send_type'])==1) ? "ارسال با پیک" : "تحویل حضوری";
            $product_send_type = (intval($product['product_send_type'])==3) ? "ارسال با پیک و یا تحویل حضوری" : $product_send_type;
            // Time
            echo '<div class="box float-right margin-right-25" style="max-height: 300px; overflow-y: scroll; overflow-x: hidden"><header><i class="fa fa-clock-o" aria-hidden="true"></i> <b>زمان و نحوه تحویل</b></header>
                    <table  class="tbl no-border-right no-border-left no-border-top"><input hidden class="productId" value="'.$productId.'">'.$product['product_delivery_time'].'&nbsp;'.$product_send_type.'<br><hr><br><b><p>تغیر اطلاعات</p></b><form action="shopy.php?type=show&productId='.$productId.'&action=time" method="post"><label>شیوه ارسال: <label><input type="checkbox" name="product_send_type_post" value="1" checked><div class="check"><div class="inside"></div></div> پیک</label>  |  <label><input type="checkbox" name="product_send_type_place" value="2"><div class="check"><div class="inside"></div></div> حضوری</label>  </label><br><br>
                <label>زمان تحویل: <input type="text" placeholder="زمان تحویل" name="time"> <label><input type="radio" name="time_radio" value="min" checked><div class="check"><div class="inside"></div></div> دقیقه</label>  |  <label><input type="radio" name="time_radio" value="hour"><div class="check"><div class="inside"></div></div> ساعت</label>  |  <label><input type="radio" name="time_radio" value="day"><div class="check"><div class="inside"></div></div> روز</label>  </label><br><br><input type="submit" class="updateTimeAndDelivery btn btn-green" value="بروزرسانی"></form>';

            echo '</table><br></div><br></div><br>
                  
                  </div>';

            // Logo
        }else{
            not_found("محصول مورد نظر یافت نشد!");
        }

    }else if($type=="shops"){ // Show Shops
        @$s = cleanInput($_GET['s']);
        $sql = (isset($_GET['s'])) ? "SELECT * FROM `shops` as shops LEFT JOIN `sections` as sections ON shops.`shop_section_id` = sections.`section_id` LEFT JOIN `cities` as cities ON shops.`shop_city_id` = cities.`city_id` LEFT JOIN `users` as users ON users.`users_ID` = shops.`shop_admin` WHERE sections.`delete` = 0 AND `shop_name` LIKE '%$s%' OR `shop_address` LIKE '%$s%' OR users.`user_display_name` LIKE '%$s%' OR `shop_telephone` LIKE '%$s%' LIMIT 50" :"SELECT * FROM `shops` as shops LEFT JOIN `sections` as sections ON shops.`shop_section_id` = sections.`section_id` LEFT JOIN `cities` as cities ON shops.`shop_city_id` = cities.`city_id` LEFT JOIN `users` as users ON users.`users_ID` = shops.`shop_admin` WHERE sections.`delete` = 0 LIMIT 50 ";
        echo ' 
        <header class="top"> <i class="fa fa-shopping-cart" aria-hidden="true"></i> فروشگاه ها <form action="'.$PHP_SELF.'"><input name="s" placeholder="جستجو"><input name="type" value="shops" hidden><input type="submit" hidden> </form></header>
        <table class="tbl">
        <tr class="top">
            <td>#</td>
            <td>اطلاعات</td>
            <td>لوگو</td>
            <td>شهر</td>
            <td>بخش</td>
            <td style="width: 150px;">عمل</td>
        </tr>
        ';
        // ✔ ✖
        $result = query($sql);
        $i = 0;
        foreach ($result as $pr){
            $i++;
            $info = query("SELECT * FROM `shops_meta` WHERE `shop_meta_shop_id`= ".$pr['shop_id']);

            foreach ($info as $print=>$key){
                $$key['shop_meta_key'] = cleanInput($key['shop_meta_value']);
            }
            if($pr['city_active']){
                $city = '<a style="color:green !important" href="city.php?type=show&cityId='.$pr['city_id'].'">'.$pr['city_name'].'</a>';
            }else{
                $city = '<a style="color:rgb(63,81,181) !important" href="city.php?type=show&cityId='.$pr['city_id'].'">'.$pr['city_name'].'</a>';
            }
            $active = ($pr['shop_active']) ? '<span class="color-red pointer unActiveShop" shopId="'.$pr['shop_id'].'" title="غیر فعال سازی">✖غیر فعال سازی</span>' : '<span class="color-green pointer activeShop" shopId="'.$pr['shop_id'].'" title="فعال سازی">فعال سازی✔</span>' ;
           $logo = query("SELECT `shop_meta_value` as logo FROM `shops_meta` WHERE `shop_meta_key` = 'logo' AND `shop_meta_shop_id` = '".$pr['shop_id']."' AND `shop_meta_active` != 0")->fetch()['logo'];
            echo '<tr>
                    <td>'.$i.'</td>
                    <td>'.$pr['shop_name'].'<br> با مدیریت <a href="users.php?type=show&userID='.$pr['shop_admin'].'">'.$pr['user_display_name'].'</a></td>
                    <td><img class="avatar avatar-140" src="'.UPLOADS.@$logo.'" alt="'.$pr['shop_name'].'" ></td>
                    <td>'.$city.'</td>
                    <td><a href="shopy.php?type=show&sectionId='.$pr['section_id'].'">'.$pr['section_name'].'</a></td>
                    <td>
                        <a style="color:rgb(0,150,136) !important" href="shopy.php?type=show&shopId='.$pr['shop_id'].'">نمایش/ویرایش</a>
                        <br>
                        '.$active.'
                    </td>
                </tr>
            ';
            unset($logo);
        }
        echo "</table>
                <!-- <a href='users.php?type=add'><div class='btn btn-green'><i class='fa fa-user-plus'></i>  افزودن کاربر</div> </a> -->
                <a href=\"shopy.php?type=addShop\"><div class=\"btn btn-green\" style=\"text-align:center;float:none; width: 179px;\">افزودن فروشگاه<i class=\"fa fa-plus\" aria-hidden=\"true\"></i> </div></a>
            ";
    }else if($type=="products"){ // Show Products
        @$s = cleanInput($_GET['s']);
        $sql = (isset($_GET['s'])) ? "SELECT * FROM `products` as products LEFT JOIN `shops` as shops ON shops.`shop_id` = products.`product_shop` WHERE `product_delete` = 0 AND  `product_plug` LIKE '%$s%' OR `product_name` LIKE '%$s%'  ORDER BY `product_id` DESC LIMIT 50" :"SELECT * FROM `products` as products LEFT JOIN `shops` as shops ON shops.`shop_id` = products.`product_shop` WHERE `product_delete` = 0 ORDER BY `product_id` DESC LIMIT 50";

        if(isset($shopId)){
            $sql = (isset($_GET['s'])) ? "SELECT * FROM `products` as products LEFT JOIN `shops` as shops ON shops.`shop_id` = products.`product_shop` WHERE `product_delete` = 0 AND `product_shop` = '$shopId' AND  `product_plug` LIKE '%$s%' OR `product_name` LIKE '%$s%'  ORDER BY `product_id` DESC" :"SELECT * FROM `products` as products LEFT JOIN `shops` as shops ON shops.`shop_id` = products.`product_shop` WHERE `product_delete` = 0 AND `product_shop` = '$shopId' ORDER BY `product_id` DESC";
        }

        echo ' 
        <header class="top"> <i class="fa fa-list-alt" aria-hidden="true"></i> محصولات <form action="'.$PHP_SELF.'"><input name="s" placeholder="جستجو"><input name="type" value="products" hidden><input type="submit" hidden> </form></header><br>
        <section class="flex">';
        // <div class="item"><h5>کباب سلطانی از <a href="http://localhost/_dezFood/admin/shopy.php?type=show&shopId=8" target="_blank">فرخی</a> با قیمت <font color="#f00">5000</font> - <font color="green">50%</font> تخفیف</h5> <font color="blue">موجود!</font> <br><a href="shopy.php?type=addProduct"><div class="btn btn-yellow" style="text-align:center;float:none; width: 50px;">نمایش</div></a></div>

        foreach (query($sql) as $pr){
            $available = ($pr['product_exist']) ? "<font color=\"blue\">موجود!</font>" : "<font color=\"red\">ناموجود!</font>";
            $hot = (!$pr['product_especial']) ? '<a href="shopy.php?type=show&productId='.$pr['product_id'].'&action=especial"><div class="btn btn-green hide" style="text-align:center;float:none; width: 60px;font-size: 8pt;">ویژه شدن</div></a>' : '<a href="shopy.php?type=show&productId='.$pr['product_id'].'&action=notEspecial"><div class="btn btn-red hide" style="text-align:center;float:none; width: 60px;font-size: 8pt !important;">عادی شدن</div></a>';
            echo ' <div class="item"><h5>'.$pr['product_name'].'  از <a href="shopy.php?type=show&shopId='.$pr['shop_id'].'" target="_blank">'.$pr['shop_name'].'</a> با قیمت <font color="#f00">'.$pr['product_price'].'</font> - <font color="green">'.$pr['product_off'].'%</font> تخفیف</h5> '.$available.' <br><a href="shopy.php?type=show&productId='.$pr['product_id'].'"><div class="btn btn-yellow hide" style="text-align:center;float:none; width: 50px;">نمایش</div></a>&nbsp;'.$hot.'</div>';
        }

        echo "</section><br><a href=\"shopy.php?type=addProduct\"><div class=\"btn btn-green\" style=\"text-align:center;float:none; width: 179px;\">افزودن محصول<i class=\"fa fa-plus\" aria-hidden=\"true\"></i> </div></a>";
    }else if($type=="hotProducts"){ // Show hot
        @$s = cleanInput($_GET['s']);
        $sql = (isset($_GET['s'])) ? "SELECT * FROM `products` as products LEFT JOIN `shops` as shops ON shops.`shop_id` = products.`product_shop` WHERE  `product_delete` = 0 AND `product_especial` AND `product_plug` LIKE '%$s%' OR `product_name` LIKE '%$s%'  ORDER BY `product_id` DESC LIMIT 50" :"SELECT * FROM `products` as products LEFT JOIN `shops` as shops ON shops.`shop_id` = products.`product_shop` WHERE `product_delete` = 0 AND `product_especial` ORDER BY `product_id` DESC LIMIT 50";

        if(isset($shopId)){
            $sql = (isset($_GET['s'])) ? "SELECT * FROM `products` as products LEFT JOIN `shops` as shops ON shops.`shop_id` = products.`product_shop` WHERE `product_delete` = 0 AND `product_shop` = '$shopId' AND `product_especial` AND  `product_plug` LIKE '%$s%' OR `product_name` LIKE '%$s%'  ORDER BY `product_id` DESC" :"SELECT * FROM `products` as products LEFT JOIN `shops` as shops ON shops.`shop_id` = products.`product_shop` WHERE `product_delete` = 0  AND `product_shop` = '$shopId' AND `product_especial` ORDER BY `product_id` DESC";
        }

        echo ' 
        <header class="top"> <i class="fa fa-list-alt" aria-hidden="true"></i> محصولات <form action="'.$PHP_SELF.'"><input name="s" placeholder="جستجو"><input name="type" value="products" hidden><input type="submit" hidden> </form></header><br>
        <section class="flex">';
        // <div class="item"><h5>کباب سلطانی از <a href="http://localhost/_dezFood/admin/shopy.php?type=show&shopId=8" target="_blank">فرخی</a> با قیمت <font color="#f00">5000</font> - <font color="green">50%</font> تخفیف</h5> <font color="blue">موجود!</font> <br><a href="shopy.php?type=addProduct"><div class="btn btn-yellow" style="text-align:center;float:none; width: 50px;">نمایش</div></a></div>

        foreach (query($sql) as $pr){
            $available = ($pr['product_exist']) ? "<font color=\"blue\">موجود!</font>" : "<font color=\"red\">ناموجود!</font>";
            $hot = (!$pr['product_especial']) ? '<a href="shopy.php?type=show&productId='.$pr['product_id'].'&action=especial"><div class="btn btn-green hide" style="text-align:center;float:none; width: 60px;font-size: 8pt;">ویژه شدن</div></a>' : '<a href="shopy.php?type=show&productId='.$pr['product_id'].'&action=notEspecial"><div class="btn btn-red hide" style="text-align:center;float:none; width: 60px;font-size: 8pt !important;">عادی شدن</div></a>';
            echo ' <div class="item"><h5>'.$pr['product_name'].'  از <a href="shopy.php?type=show&shopId='.$pr['shop_id'].'" target="_blank">'.$pr['shop_name'].'</a> با قیمت <font color="#f00">'.$pr['product_price'].'</font> - <font color="green">'.$pr['product_off'].'%</font> تخفیف</h5> '.$available.' <br><a href="shopy.php?type=show&productId='.$pr['product_id'].'"><div class="btn btn-yellow hide" style="text-align:center;float:none; width: 50px;">نمایش</div></a>&nbsp;'.$hot.'</div>';
        }

        echo "</section><br><a href=\"shopy.php?type=addProduct\"><div class=\"btn btn-green\" style=\"text-align:center;float:none; width: 179px;\">افزودن محصول<i class=\"fa fa-plus\" aria-hidden=\"true\"></i> </div></a>";
    }else if($type=="addShop"){ // add shop
        if(isset($addShop)){
            unset($addShop);
            ok('با موفقیت افزوده شد <a href="shopy.php?type=show&shopId='.$db->lastInsertId().'"> نمایش فروشگاه </a>');
        }else if(isset($notAddShop)){
            unset($notAddShop);
            error("افزوده نشد!");
        }
        
        echo '<header class="top"><i class="fa fa-plus" aria-hidden="true"></i> افزودن فروشگاه</header>
              <div class="form">
              <form action="shopy.php?type=addShop&action=addShop" method="post">
                <label>نام فروشگاه: <input type="text" placeholder="نام فروشگاه" name="name"></label>
                <label>شهر: '.citiesSelect().'</label>
                <label>بخش: '.sections_select().'</label><br><br>
                <label>دسته بندی فروشگاه: '.shop_category_select().'</label><br><br>
                <label>مدیریت: <input type="text" placeholder="آیدی مدیریت" name="user"></label>
                <label>کمترین میزان خرید: <input type="text" placeholder="کمترین میزان خرید" name="shop_min"></label>
                <label>درصد بازاریابی: <input type="text" placeholder="درصد بازاریابی" name="percent"></label><br><br>
                <label>آدرس: <textarea type="text" style="max-width: 200px;" placeholder="آدرس" name="address"></textarea></label>
                <label>تلفن: <textarea type="text" style="max-width: 200px;" placeholder="تلفن" name="telephone"></textarea></label>
                <input type="submit" value="تایید" class=\'btn btn-green float-left\'>
              </form>
            </div>
        ';
    }else if($type=="editShop" && isset($shopId) && !empty($shopId)){
        $shopId = intval($shopId);
        $result = query("SELECT * FROM `shops` WHERE `shop_id` = '$shopId'");
        if($result->rowCount() >= 1){
            $result = $result->fetch();
            foreach ($result as $pr=>$key){
                $$pr = cleanInput($key);
            }
            if(isset($update_shop)){
                ok("با موفقیت بروزرسانی شد!");
                unset($update_shop);
            }
            echo '<header class="top"><i class="fa fa-edit" aria-hidden="true"></i> ویرایش اطلاعات</header>
              <div class="form">
              <form action="shopy.php?type=editShop&action=update&shopId='.$shopId.'" method="post">
                <label>نام فروشگاه: <input type="text" value="'.$shop_name.'" placeholder="نام فروشگاه" name="name"></label>
                <label>شهر: '.citiesSelect($shop_city_id).'</label>
                <label>بخش: '.sections_select($shop_section_id).'</label><br><br>
                <label>دسته بندی: '.shop_category_select().'</label><br><br>
                <label>مدیریت: <input type="text" value="'.$shop_admin.'" placeholder="آیدی مدیریت" name="user"></label>
                <label>کمترین میزان خرید: <input type="text" value="'.$shop_min.'" placeholder="کمترین میزان خرید" name="shop_min"></label>
                <label>هزینه پست: <input type="text" value="'.$delivery_cost.'" placeholder="هزینه پست" name="delivery_cost"></label><br><br>
                <label>کیلومتر اضافه: <input type="text" value="'.$delivery_add_cost_km.'" placeholder="km" name="add_cost_km"></label><br><br>
                <label>هزینه کیلومتر اضافه: <input type="text" value="'.$delivery_add_cost_price.'" placeholder="هزینه کیلومتر اضافه" name="delivery_add_cost_price"></label><br><br>
                <label>میزان پست رایگان: <input type="text" value="'.$delivery_free.'" placeholder="میزان پست رایگان" name="delivery_free"></label>
                <label>درصد بازاریابی: <input type="text" value="'.$percent.'" placeholder="درصد بازاریابی" name="percent"></label><br><br>
                <label>مختصات جغرافیایی: <input type="text" value="'.$shop_lat_lng.'" placeholder="مختصات جغرافیایی" name="shop_lat_lng"></label><br><br>
                <label>آدرس: <textarea type="text" class="textarea200"  placeholder="آدرس" name="address">'.$shop_address.'</textarea></label>
                <label>تلفن: <textarea type="text" class="textarea200 directionLtr" placeholder="تلفن" name="telephone">'.$shop_telephone.'</textarea></label>
                <input type="submit" value="تایید" class=\'btn btn-green float-left\'>
              </form>
            </div><br>
            <a href=\'shopy.php?type=show&shopId='.$shopId.'\'><div class=\'btn btn-green\'><i class=\'fa fa-shopping-cart\'></i>  بازگشت به فروشگاه</div>
        ';
        }else{
            error("فروشگاه یافت نشد!");
        }

    }else if($type=="addProduct") {  // Add Product
        if (isset($addProduct)) {
            unset($addProduct);
            ok('با موفقیت افزوده شد <a href="shopy.php?type=show&productId=' . $add_im . '"> نمایش محصول </a>');
        } else if (isset($notAddProduct)) {
            unset($notAddShop);
            error("افزوده نشد!");
        }
        $sections = query("SELECT * FROM `sections` WHERE !`delete`");
        $select_category = "<select name='category[]' multiple>";
        foreach ($sections as $pr){
            $select_category = $select_category."<option disabled>&nbsp; - ".$pr['section_name']."</option>";
            $category = query("SELECT * FROM `sections_meta` WHERE `section_meta_key` = 'category' AND `section_meta_section_id` = ".$pr['section_id']);
            foreach ($category as $pri){
                $select_category =  $select_category."<option value='".$pri['section_meta_id']."'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -- ".$pri['section_meta_value']."</option>";
            }
        }
        $select_category = $select_category."</select>";
        echo '<header class="top"><i class="fa fa-plus" aria-hidden="true"></i> افزودن محصول</header>
              <div class="form">
              <form action="shopy.php?type=addProduct&action=addProduct" method="post" enctype="multipart/form-data">
                <label>نام محصول: <input type="text" placeholder="نام محصول" name="name"></label>&nbsp;
                <label>کد فروشگاه: <input type="text" placeholder="کد فروشگاه" value="' . @$shopId . '" name="shopId"></label><br><br>
                 <label>دسته: '.$select_category.'</label>&nbsp;&nbsp;
                <label>قیمت تخفیف: <input type="text" placeholder="قیمت تخفیف" name="product_off"></label><br><br>
                <label>قیمت محصول: <input type="text" placeholder="قیمت محصول" value="" name="price"></label>&nbsp;
                <label>متن آدرس انگلیسی(plug): <input type="text" placeholder="متن آدرس انگلیسی" value="" name="plug"></label><br><br>
                <label>شیوه ارسال: <label><input type="checkbox" name="product_send_type_post" value="1" checked><div class="check"><div class="inside"></div></div> پیک</label>  |  <label><input type="checkbox" name="product_send_type_place" value="2"><div class="check"><div class="inside"></div></div> حضوری</label>  </label><br><br>
                <label>زمان تحویل: <input type="text" placeholder="زمان تحویل" name="time"> <label><input type="radio" name="time_radio" value="min" checked><div class="check"><div class="inside"></div></div> دقیقه</label>  |  <label><input type="radio" name="time_radio" value="hour"><div class="check"><div class="inside"></div></div> ساعت</label>  |  <label><input type="radio" name="time_radio" value="day"><div class="check"><div class="inside"></div></div> روز</label>  </label><br><br>
                <label><b>تصاویر محصول(حداقل یک و حداکثر 3):</b></label><br>
                <input type="file" name="file[]"><br><br>
                <input type="file" name="file[]"><br><br>
                <input type="file" name="file[]">
                
                <input type="submit" value="تایید" class=\'btn btn-green float-left\'>
              </form> 
              </div><br>';
        if (!empty($shopId)) {
            echo "<a href='shopy.php?type=show&shopId=$shopId'><div class='btn btn-green'><i class='fa fa-shopping-cart'></i>  بازگشت به فروشگاه</div><br><br>";
        }

    }else if($type=="editProduct" && isset($productId)) {  // Edit Product
        $productId = intval($productId);
        if(@$action == "editProduct" && isset($name) && !empty($name)&& isset($plug) && !empty($plug)&& isset($price) && !empty($price)&& isset($shopId) && !empty($shopId)&& isset($product_off) && isset($product_description)){
            product_do("name",$productId,$name);
            product_do("plug",$productId,$plug);
            product_do("price",$productId,$price);
            product_do("shopId",$productId,$shopId);
            product_do("off",$productId,$product_off);
            product_do("description",$productId,$product_description);
            product_do("deleteAllCategories",$productId);
            foreach (@$_POST['category'] as $cat){
                product_do("addCategory",$productId,intval($cat),query("SELECT `product_shop` as product_shop FROM `products` WHERE `product_id` = '$productId'")->fetch()['product_shop']);
            }
            ok('با موفقیت بروزرسانی شد <a href="shopy.php?type=show&productId=' . $productId . '"> نمایش محصول </a>');
        }

        $data = query("SELECT * FROM `products` WHERE `product_id` = '$productId' ");
        if($data->rowCount() == 1){
            $data = $data->fetch();





            $product_categories = array();
            foreach(query("SELECT * FROM `products_meta` WHERE `product_meta_key` = 'category' AND `product_meta_active` AND `product_id` = '$productId'") as $cat){
                array_push($product_categories,$cat['product_meta_value']);
            }


            $sections = query("SELECT * FROM `sections` WHERE !`delete`");
            $select_category = "<select name='category[]' multiple>";
            foreach ($sections as $pr){
                $select_category = $select_category."<option disabled>&nbsp; - ".$pr['section_name']."</option>";
                $category = query("SELECT * FROM `sections_meta` WHERE `section_meta_key` = 'category' AND `section_meta_section_id` = ".$pr['section_id']);
                foreach ($category as $pri){
                    if(in_array($pri['section_meta_id'],$product_categories)){
                        $select_category =  $select_category."<option value='".$pri['section_meta_id']."' selected>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -- ".$pri['section_meta_value']."</option>";
                    }else{
                        $select_category =  $select_category."<option value='".$pri['section_meta_id']."'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -- ".$pri['section_meta_value']."</option>";
                    }

                }
            }
            $select_category = $select_category."</select>";

            echo '<header class="top"><i class="fa fa-plus" aria-hidden="true"></i> افزودن محصول</header>
              <div class="form">
              <form action="shopy.php?type=editProduct&action=editProduct&productId='.$productId.'" method="post" enctype="multipart/form-data">
                <label>نام محصول: <input type="text" value="' . $data['product_name'] . '" placeholder="نام محصول" name="name"></label>&nbsp;
                <label>کد فروشگاه: <input type="text" placeholder="کد فروشگاه" value="' . $data['product_shop'] . '" name="shopId"></label><br><br>
                 <label>دسته: '.$select_category.'</label>&nbsp;&nbsp;
                <label>قیمت تخفیف: <input type="text" value="' . $data['product_off'] . '" placeholder="قیمت تخفیف" name="product_off"></label><br><br>
                <label>قیمت محصول: <input type="text" value="' . $data['product_price'] . '" placeholder="قیمت محصول" value="" name="price"></label>&nbsp;
                <label>متن آدرس انگلیسی(plug): <input type="text" value="' . $data['product_plug'] . '" placeholder="متن آدرس انگلیسی" value="" name="plug"></label><br><br>
                
                <textarea name="product_description" style="width:500px; height:250px;">' . $data['product_description'] . '</textarea>
                <br><br>
                <input type="submit" value="تایید" class=\'btn btn-green float-left\'>
                <br>
              </form> 
              </div><br>';
            if (!empty($shopId)) {
                echo "<a href='shopy.php?type=show&shopId=$shopId'><div class='btn btn-green'><i class='fa fa-shopping-cart'></i>  بازگشت به فروشگاه</div><br><br>";
            }

        }else{
            error("محصول یافت نشد!");
        }

    }
}
include 'includes/footer.php';
?>
