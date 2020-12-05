<?php

function isAdmin($t = 'redirect'){
    if ( is_session_started() === FALSE ) session_start();
    if(isset($_SESSION['user_id_admin_panel'])){
        return true;
    }else{
        $a = ($t=='redirect') ? header("Location:".ADDR."admin/login.php") :  header("location:".ADDR.$t);
    }
}

function permission($name,$redirect = false){
    global $user_status;
    if($user_status == 3){

        return true;
    }else{
        global $user_permission;
        if( in_array($name,$user_permission)){
            return true;
        }else{
            if($redirect){
                header("Location: index.php");
            }
            return false;
        }

    }
}

function not_found($text){
    echo '<header class="top"> <i class="fa fa-close" aria-hidden="true"></i> '.$text.' </header>';
}
function cities($sectionId){
    $cities_print = array();
    $info = query("SELECT * FROM `sections_meta` WHERE `meta_active` AND `section_meta_key` = 'city' AND `section_meta_section_id`= ".$sectionId);
    foreach ($info as $print){
        $city = query("SELECT * FROM `cities` WHERE `city_id` = ".$print['section_meta_value']);
        $city = $city->fetch();
        if($city['city_active']){
            array_push($cities_print,'<a cityId='.$print['section_meta_value'].' style="color:green !important" href="city.php?type=show&cityId='.$print['section_meta_value'].'">'.$city['city_name'].'</a>') ;
        }else{
            array_push($cities_print,'<a cityId='.$print['section_meta_value'].' style="color:rgb(63,81,181) !important" href="city.php?type=show&cityId='.$print['section_meta_value'].'">'.$city['city_name'].'</a>');
        }
    }
    return $cities_print;
}
function city_state($id = 0,$active = true){
    if($active){
        query("UPDATE `cities` SET `city_active`= 1 WHERE `city_id` = ".intval($id));
    }else{
        query("UPDATE `cities` SET `city_active`= 0 WHERE `city_id` = ".intval($id));
    }

}

function UpdateCityHome($cid){
    global $db;
    $cid = intval($cid);

    $array = array();
    /* $farray = array();
    array_push($farray,array(
        'last_version_code' => 1,
        'last_version_code_works' => 1,
        'uploads' => UPLOADS,
    ));

    array_push($array,array("information" => $farray));
*/
    // Top Splash
    $farray = array();
    $result = query("SELECT * FROM `cities_meta` WHERE `city_meta_city_id` = ".$cid." AND `city_meta_active` AND `city_meta_key` = 'top_splash'");
    foreach ($result as $pr){
        if (startsWith($pr['city_meta_data'],"shop:")){
            $shopid = substr($pr['city_meta_data'],5,strlen($pr['city_meta_data'])) ;
            $type = "shop";
        }else if (startsWith($pr['city_meta_data'],"section:")){
            $shopid = substr($pr['city_meta_data'],8,strlen($pr['city_meta_data'])) ;
            $type = "section";
        }else if (startsWith($pr['city_meta_data'],"category:")){
            $shopid = substr($pr['city_meta_data'],9,strlen($pr['city_meta_data'])) ;
            $type = "category";
        }else if (startsWith($pr['city_meta_data'],"link:")){
            $shopid = substr($pr['city_meta_data'],5,strlen($pr['city_meta_data'])) ;
            $type = "link";
        }else if (startsWith($pr['city_meta_data'],"news:")){
            $shopid = substr($pr['city_meta_data'],5,strlen($pr['city_meta_data'])) ;
            $type = "news";
        }else {
            $shopid = 0;
            $type = "nothing";
        }
        array_push($farray,array(
            "b" => $pr['city_meta_value'],
            't' => $type,
            'i' => $shopid
        ));
    }
    array_push($array,array('top_splash' => $farray));

    // Bottom Splash
    $farray = array();
    $result = query("SELECT * FROM `cities_meta` WHERE `city_meta_city_id` = ".$cid." AND `city_meta_active` AND `city_meta_key` = 'bottom_splash'");
    foreach ($result as $pr){
        if (startsWith($pr['city_meta_data'],"shop:")){
            $shopid = substr($pr['city_meta_data'],5,strlen($pr['city_meta_data'])) ;
            $type = "shop";
        }else if (startsWith($pr['city_meta_data'],"section:")){
            $shopid = substr($pr['city_meta_data'],8,strlen($pr['city_meta_data'])) ;
            $type = "section";
        }else if (startsWith($pr['city_meta_data'],"category:")){
            $shopid = substr($pr['city_meta_data'],9,strlen($pr['city_meta_data'])) ;
            $type = "category";
        }else if (startsWith($pr['city_meta_data'],"link:")){
            $shopid = substr($pr['city_meta_data'],5,strlen($pr['city_meta_data'])) ;
            $type = "link";
        }else if (startsWith($pr['city_meta_data'],"news:")){
            $shopid = substr($pr['city_meta_data'],5,strlen($pr['city_meta_data'])) ;
            $type = "news";
        }else {
            $shopid = 0;
            $type = "nothing";
        }
        array_push($farray,array(
            "b" => $pr['city_meta_value'],
            't' => $type,
            'i' => $shopid
        ));
    }
    array_push($array,array('bottom_splash' => $farray));

    // Especial
    $farray = array();
    $especial_title = $db->query("SELECT `city_meta_value` as especial_title FROM `cities_meta` WHERE `city_meta_key` = 'especial_title' AND `city_meta_active` AND `city_meta_city_id` = ".$cid)->fetch()['especial_title'];
    if($especial_title == "" || $especial_title == Null) $especial_title = "ویژه‌ها";
    array_push($farray,array("ename" => $especial_title));

    $result = query("SELECT `shop_id` as shop_id FROM `shops` WHERE `shop_active` AND `shop_city_id` = ".$cid);
    foreach ($result as $pr){
        $shop_id = intval($pr['shop_id']);
        $hot = query("SELECT `product_id`,`product_price`,`product_off`,`product_name` FROM `products` WHERE `product_shop` = '".$shop_id."' AND `product_especial` AND `product_active` AND !`product_delete`  ");
        foreach ($hot as $hp){
            $image = query("SELECT `product_meta_value` as image FROM `products_meta` WHERE `product_id` = '".$hp['product_id']."' AND `product_meta_active` AND `product_meta_key` = 'image' LIMIT 1")->fetch()['image'];
            array_push($farray,array(
                "i" => $hp['product_id'],
                "p" => $hp['product_price'],
                "np" => ($hp['product_off'] == 0) ? $hp['product_price'] : $hp['product_off'] ,
                "n" => $hp['product_name'] ,
                "im" => UPLOADS.$image
            ));
        }
    }

    array_push($array,array('especial' => $farray));


    // Off
    $farray = array();
    $result = query("SELECT `shop_id` as shop_id FROM `shops` WHERE `shop_active` AND `shop_city_id` = ".$cid);
    foreach ($result as $pr){
        $shop_id = intval($pr['shop_id']);
        $hot = query("SELECT `product_id`,`product_price`,`product_off`,`product_name` FROM `products` WHERE `product_shop` = '".$shop_id."' AND `product_active` AND !`product_delete` AND !`product_especial` AND  `product_off` ");
        foreach ($hot as $hp){
            $image = query("SELECT `product_meta_value` as image FROM `products_meta` WHERE `product_id` = '".$hp['product_id']."' AND `product_meta_active` AND `product_meta_key` = 'image' LIMIT 1")->fetch()['image'];
            array_push($farray,array(
                "i" => $hp['product_id'],
                "p" => $hp['product_price'],
                "np" => ($hp['product_off'] == 0) ? $hp['product_price'] : $hp['product_off'] ,
                "n" => $hp['product_name'] ,
                "im" => UPLOADS.$image
            ));
        }
    }

    array_push($array,array('on-sale' => $farray));

    $update['city_meta_active'] = 0;
    query(queryUpdate('cities_meta',$update," WHERE `city_meta_city_id` = ".$cid." AND `city_meta_key` = 'homeData' "));

    $insert['city_meta_city_id'] = $cid;
    $insert['city_meta_key'] = 'homeData';
    $insert['city_meta_value'] = base64_encode(json_encode($array,JSON_UNESCAPED_UNICODE));
    $insert['city_meta_date'] = get_date();
    $insert['city_meta_active'] = 1;
    query(queryInsert('cities_meta',$insert));
}


function user_lock($id = 0,$block = true){
    if($block){
        query("UPDATE `users` SET `user_approve`= 0 WHERE `users_ID` = ".intval($id));
    }else{
        query("UPDATE `users` SET `user_approve`= 1 WHERE `users_ID` = ".intval($id));
    }

}
function addCity($name,$state_id){
    global $db;
    $result = $db->prepare("INSERT INTO `cities`(`city_name`,`city_state`) VALUES (?,?)");
    $result->bindValue(1,cleanInput($name));
    $result->bindValue(2,intval($state_id));
    return $result->execute();
}
function user_update($type = '',$user_id = 0,$data = ''){
    global $db;
    if(isset($type) && !empty($type)){
        $user_id = intval($user_id);
        $data = cleanInput($data);
        switch ($type){
            case "card";
                query("DELETE FROM `users_meta` WHERE `users_meta_user_id` = '$user_id' AND `users_meta_key` = 'card'");
                $result = $db->prepare("INSERT INTO `users_meta`(`users_meta_user_id`, `users_meta_key`, `users_meta_value`) VALUES (?,'card',?)");
                $result->bindValue(1,$user_id);
                $result->bindValue(2,$data);
                $result->execute();
                break;
            case "position";
                query("DELETE FROM `users_meta` WHERE `users_meta_user_id` = '$user_id' AND `users_meta_key` = 'position'");
                $result = $db->prepare("INSERT INTO `users_meta`(`users_meta_user_id`, `users_meta_key`, `users_meta_value`) VALUES (?,'position',?)");
                $result->bindValue(1,$user_id);
                $result->bindValue(2,$data);
                $result->execute();
                break;
        }
    }
}
function addUser($username,$password,$displayName,$phone){
    global $db;
    $username = username($username);
    $phone = phone($phone);
    $result = query("SELECT * FROM `users` WHERE `user_login` = '$username'");
    if($result->rowCount() >= 1){
        error("نام کاربری تکراری است!");
    }else{
        $result = query("SELECT * FROM `users` WHERE `user_phone` = '$phone'");
        if($result->rowCount() >= 1){
            error("تلفن همراه تکراری میباشد.");
        }else {
            $date = date("Y-m-d H:i:s",time());
            $result = $db->prepare("INSERT INTO `users`(`user_login`, `user_pass`, `user_display_name`, `user_phone`, `user_registered`, `user_approve`) VALUES (?,?,?,?,?,?)");
            $result->bindValue(1,username($username));
            $result->bindValue(2,password($password));
            $result->bindValue(3,cleanInput($displayName));
            $result->bindValue(4,$phone);
            $result->bindValue(5,$date);
            $result->bindValue(6,'yes');
            return $result->execute();
        }
    }


}
function addShop($name,$city,$section,$user,$address,$telephone,$percent,$shop_category,$shop_min = 0){

    global $db,$admin_id;
    $date = date("Y-m-d");
    $result = $db->prepare("INSERT INTO `shops`(`shop_section_id`, `shop_city_id`, `shop_name`, `shop_address`, `shop_telephone`, `shop_create_date`, `shop_create_by`, `shop_admin`,`percent`,`shop_category_id`,`shop_min`) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
    $result->bindValue(1,intval($section));
    $result->bindValue(2,intval($city));
    $result->bindValue(3,cleanInput($name));
    $result->bindValue(4,cleanInput($address));
    $result->bindValue(5,cleanInput($telephone));
    $result->bindValue(6,$date);
    $result->bindValue(7,intval($admin_id));
    $result->bindValue(8,intval($user));
    $result->bindValue(9,floatval($percent));
    $result->bindValue(10,intval($shop_category));
    $result->bindValue(11,intval($shop_min));
    return $result->execute();
}
function addProduct($name_pro,$plug,$shopId,$price,$product_off,$product_send_type,$time,$time_radio){
    global $db,$admin_id;
    $date = date("Y-m-d");
    if($time_radio=="min"){
        $time_radio = 1;
    }else if($time_radio == "hour"){
        $time_radio = 60;
    }else if($time_radio == "day"){
        $time_radio = " روز";
    }
    $time = $time*$time_radio;
    $add['product_shop'] = intval($shopId);
    $add['product_plug'] = cleanInput($plug);
    $add['product_price'] = floatval($price);
    $add['product_off'] = intval($product_off);
    $add['product_name'] = cleanInput($name_pro);
    $add['product_send_type'] = intval($product_send_type);
    $add['product_delivery_time'] = cleanInput($time);
    $add['product_create_date'] = get_date();
    $add['product_create_user_id'] = $admin_id;

    return query(queryInsert('products',$add));

}
function product_do($type,$product_id,$product_do='',$other_data = ''){
    global $admin_id,$db;
    $product_id = intval($product_id);
    $product_do = cleanInput($product_do);
    $other_data = cleanInput($other_data);
    switch ($type) {
        case "addCategory":
            /*$result = $db->prepare("INSERT INTO `products_meta`(`product_id`, `product_meta_key`, `product_meta_value`) VALUES (:product_id,'category',:category_id)");
            $result->bindParam(":product_id",$product_id,PDO::PARAM_INT);
            $result->bindParam(":category_id",$product_do,PDO::PARAM_INT);
            $result->execute(); */
            $add['relation_product_id'] = intval($product_id);
            $add['relation_category_id'] = intval($product_do);
            $add['relation_shop_id'] = intval($other_data);
            query(queryInsert('products_relation',$add));
            break;
        case "deleteAllCategories":
            /* $result = $db->prepare("UPDATE `products_meta` SET `product_meta_active`= 0 WHERE `product_meta_key` = 'category' AND `product_id` = :product_id");
            $result->bindParam(":product_id",$product_id,PDO::PARAM_INT);
            $result->execute();
            */
            query("DELETE FROM `products_relation` WHERE `relation_product_id` = ".intval($product_id));
            break;
        case "activeProduct":
            $result = $db->prepare("UPDATE `products` SET `product_active` = 1 WHERE `product_id` = :product_id");
            $result->bindParam(":product_id",$product_id,PDO::PARAM_INT);
            $result->execute();
            break;
        case "closeProduct":
            $result = $db->prepare("UPDATE `products` SET `product_active` = 0 WHERE `product_id` = :product_id");
            $result->bindParam(":product_id",$product_id,PDO::PARAM_INT);
            $result->execute();
            break;
        case "notExist":
            $result = $db->prepare("UPDATE `products` SET `product_exist` = 0 WHERE `product_id` = :product_id");
            $result->bindParam(":product_id",$product_id,PDO::PARAM_INT);
            $result->execute();
            break;
        case "exist":
            $result = $db->prepare("UPDATE `products` SET `product_exist` = 1 WHERE `product_id` = :product_id");
            $result->bindParam(":product_id",$product_id,PDO::PARAM_INT);
            $result->execute();
            break;
        case "notEspecial":
            $result = $db->prepare("UPDATE `products` SET `product_especial` = 0 WHERE `product_id` = :product_id");
            $result->bindParam(":product_id",$product_id,PDO::PARAM_INT);
            $result->execute();
            break;
        case "especial":
            $result = $db->prepare("UPDATE `products` SET `product_especial` = 1 WHERE `product_id` = :product_id");
            $result->bindParam(":product_id",$product_id,PDO::PARAM_INT);
            $result->execute();
            break;
        case "addImage":
            $image_name = cleanInput($product_do);
            $result = $db->prepare("INSERT INTO `products_meta`(`product_id`, `product_meta_key`, `product_meta_value`) VALUES (:product_id,'image',:image)");
            $result->bindParam(":product_id",$product_id,PDO::PARAM_INT);
            $result->bindParam(":image",$image_name,PDO::PARAM_STR);
            $result->execute();
            break;
        case "deleteProductImage":
            query("UPDATE `products_meta` SET `product_meta_active`=0 WHERE `product_meta_id` = '$product_id'");
            break;
        case "time":
            if($product_do=="min"){
                $product_do = " دقیقه";
            }else if($product_do == "hour"){
                $product_do = " ساعت";
            }else if($product_do == "day"){
                $product_do = " روز";
            }
            $other_data = $other_data.$product_do;
            $result = $db->prepare("UPDATE `products` SET `product_delivery_time` = :dtime WHERE `product_id` = :product_id");
            $result->bindParam(":product_id",$product_id,PDO::PARAM_INT);
            $result->bindParam(":dtime",$other_data,PDO::PARAM_STR);
            $result->execute();
            break;
        case "send_type":
            $result = $db->prepare("UPDATE `products` SET `product_send_type` = :send_type WHERE `product_id` = :product_id");
            $result->bindParam(":send_type",$product_do,PDO::PARAM_INT);
            $result->bindParam(":product_id",$product_id,PDO::PARAM_INT);
            $result->execute();
            break;
        case "name":
            $result = $db->prepare("UPDATE `products` SET `product_name` = :text WHERE `product_id` = :product_id");
            $result->bindParam(":text",$product_do,PDO::PARAM_STR);
            $result->bindParam(":product_id",$product_id,PDO::PARAM_INT);
            $result->execute();
            break;
        case "plug":
            $result = $db->prepare("UPDATE `products` SET `product_plug` = :text WHERE `product_id` = :product_id");
            $result->bindParam(":text",$product_do,PDO::PARAM_STR);
            $result->bindParam(":product_id",$product_id,PDO::PARAM_INT);
            $result->execute();
            break;
        case "off":
            $result = $db->prepare("UPDATE `products` SET `product_off` = :text WHERE `product_id` = :product_id");
            $result->bindParam(":text",$product_do,PDO::PARAM_INT);
            $result->bindParam(":product_id",$product_id,PDO::PARAM_INT);
            $result->execute();
            break;
        case "shopId":
            $result = $db->prepare("UPDATE `products` SET `product_shop` = :text WHERE `product_id` = :product_id");
            $result->bindParam(":text",$product_do,PDO::PARAM_INT);
            $result->bindParam(":product_id",$product_id,PDO::PARAM_INT);
            $result->execute();
            break;
        case "price":
            $result = $db->prepare("UPDATE `products` SET `product_price` = :text WHERE `product_id` = :product_id");
            $result->bindParam(":text",$product_do,PDO::PARAM_INT);
            $result->bindParam(":product_id",$product_id,PDO::PARAM_INT);
            $result->execute();
            break;
        case "description":
            $result = $db->prepare("UPDATE `products` SET `product_description` = :text WHERE `product_id` = :product_id");
            $result->bindParam(":text",$product_do,PDO::PARAM_STR);
            $result->bindParam(":product_id",$product_id,PDO::PARAM_INT);
            $result->execute();
            break;
    }
}
function section_do($type,$section_do,$data = ''){
    global $admin_id;
    switch ($type){
        case "delete":
            query("UPDATE `sections` SET `delete` = 1 WHERE `section_id` = ".intval($section_do));
            //query("DELETE FROM `sections_meta` WHERE `section_meta_section_id` = ".intval($section_do));
            break;
        case "addSection":
            $sectionName = cleanInput($section_do);
            $result = query("SELECT * FROM `sections` WHERE `active` = 'yes' AND `section_name` = '$sectionName'");
            if($result->rowCount() >= 1){
                echo 'repeat';
            }else{
                query("INSERT INTO `sections`(`section_name`,`section_create_by`) VALUES ('$sectionName','$admin_id')");
                return "ok";
            }
            break;
        case "active":
            query("UPDATE `sections` SET `active` = 1 WHERE `section_id` = ".intval($section_do));
            break;
        case "unActive":
            query("UPDATE `sections` SET `active` = 0 WHERE `section_id` = ".intval($section_do));
            break;
        case "logo":
            $up['meta_active'] = 0;
            query(queryUpdate('sections_meta',$up," WHERE `section_meta_key` = 'banner' AND `section_meta_section_id` = ".intval($section_do)));

            $add['section_meta_key'] = 'banner';
            $add['section_meta_section_id'] = intval($section_do);
            $add['section_meta_value'] = cleanInput($data);
            $add['meta_active'] = 1;
            query(queryInsert('sections_meta',$add));

            break;
    }
}
function category_do($type,$category_do){
    switch ($type){
        case "unActive":
            query("UPDATE `sections_meta` SET `meta_active`=0 WHERE `section_meta_id` = '$category_do'");
            break;
        case "active":
            query("UPDATE `sections_meta` SET `meta_active`=1 WHERE `section_meta_id` = '$category_do'");
            break;
        case "unActiveShop":
            query("UPDATE `sections_meta` SET `meta_active`=0 WHERE `section_meta_id` = '$category_do'");
            break;
        case "activeShop":
            query("UPDATE `sections_meta` SET `meta_active`=1 WHERE `section_meta_id` = '$category_do'");
            break;
    }
}
function comment_do($type,$c_id,$data = ''){
    global $admin_id;
    switch ($type){
        case "reject":
            $c_id = intval($c_id);
            $comment['comment_approve'] = 0;
            $comment['comment_approve_by'] = $admin_id; // ADMIN ID
            $comment['comment_approve_date'] = get_date(); // ADMIN ID
            query(queryUpdate('comments',$comment,'WHERE `comment_id` = '.$c_id.';'));
            break;
        case "accept":
            $c_id = intval($c_id);
            $comment['comment_approve'] = 1;
            $comment['comment_approve_by'] = $admin_id; // ADMIN ID
            $comment['comment_approve_date'] = get_date(); // ADMIN ID
            query(queryUpdate('comments',$comment,'WHERE `comment_id` = '.$c_id.';'));
            break;
    }
}
function shop_do($type,$shopId,$otherInfo = ''){
    $shopId = intval($shopId);
    switch ($type){
        case "unActive":
            query("UPDATE `shops` SET `shop_active`=0 WHERE `shop_id` = '$shopId'");
            break;

        case "active":
            query("UPDATE `shops` SET `shop_active`=1 WHERE `shop_id` = '$shopId'");
            break;

        case "deleteShopImage":
            query("UPDATE `shops_meta` SET `shop_meta_active`=0 WHERE `shop_meta_id` = '$shopId'");
            break;

        case "addImage":
            $otherInfo = cleanInput($otherInfo);
            query("INSERT INTO `shops_meta`(`shop_meta_shop_id`, `shop_meta_key`, `shop_meta_value`) VALUES ('$shopId','image','$otherInfo')");
            break;

        case "logo":
            $otherInfo = cleanInput($otherInfo);
            query("UPDATE `shops_meta` SET `shop_meta_active`= 0 WHERE `shop_meta_key` = 'logo' AND `shop_meta_shop_id` = '$shopId'");
            query("INSERT INTO `shops_meta`(`shop_meta_shop_id`, `shop_meta_key`, `shop_meta_value`) VALUES ('$shopId','logo','$otherInfo')");
            break;
        case "shop_category":
            $otherInfo = intval($otherInfo);
            query("UPDATE `shops` SET `shop_category_id`='$otherInfo' WHERE `shop_id` = '$shopId'");
            break;
            break;

        case "updateName":
            $otherInfo = cleanInput($otherInfo);
            query("UPDATE `shops` SET `shop_name`='$otherInfo' WHERE `shop_id` = '$shopId'");
            break;

        case "updateCity":
            $otherInfo = intval($otherInfo);
            query("UPDATE `shops` SET `shop_city_id`='$otherInfo' WHERE `shop_id` = '$shopId'");
            break;

        case "updateSection":
            $otherInfo = intval($otherInfo);
            query("UPDATE `shops` SET `shop_section_id`='$otherInfo' WHERE `shop_id` = '$shopId'");
            break;

        case "updatePercent":
            $otherInfo = floatval($otherInfo);
            query("UPDATE `shops` SET `percent`='$otherInfo' WHERE `shop_id` = '$shopId'");
            break;
        
        case "updateAdmin":
            $otherInfo = intval($otherInfo);
            query("UPDATE `shops` SET `shop_admin`='$otherInfo' WHERE `shop_id` = '$shopId'");
            break;

        case "updateTelephone":
            $otherInfo = cleanInput($otherInfo);
            query("UPDATE `shops` SET `shop_telephone`='$otherInfo' WHERE `shop_id` = '$shopId'");
            break;

        case "updateAddress":
            $otherInfo = cleanInput($otherInfo);
            query("UPDATE `shops` SET `shop_address`='$otherInfo' WHERE `shop_id` = '$shopId'");
            break;

        case "updateDeliveryCost":
            $otherInfo = floatval($otherInfo);
            query("UPDATE `shops` SET `delivery_cost`='$otherInfo' WHERE `shop_id` = '$shopId'");
            break;

        case "updateDeliveryFree":
            $otherInfo = floatval($otherInfo);
            query("UPDATE `shops` SET `delivery_free`='$otherInfo' WHERE `shop_id` = '$shopId'");
            break;

        case "updateAddCostKm":
            $otherInfo = floatval($otherInfo);
            query("UPDATE `shops` SET `delivery_add_cost_km`='$otherInfo' WHERE `shop_id` = '$shopId'");
            break;

        case "delivery_add_cost_price":
            $otherInfo = floatval($otherInfo);
            query("UPDATE `shops` SET `delivery_add_cost_price`='$otherInfo' WHERE `shop_id` = '$shopId'");
            break;

        case "shop_lat_lng":
            $otherInfo = cleanInput($otherInfo);
            query("UPDATE `shops` SET `shop_lat_lng`='$otherInfo' WHERE `shop_id` = '$shopId'");
            break;
        case "shop_min":
            $otherInfo = intval($otherInfo);
            query("UPDATE `shops` SET `shop_min`='$otherInfo' WHERE `shop_id` = '".$shopId."'");
            break;
    }
}

// getSymbolByQuantity(disk_total_space("/"))
// getSymbolByQuantity(disk_free_space("/"))
function getSymbolByQuantity($bytes) {
    $symbols = array('B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB');
    $exp = floor(log($bytes)/log(1024));

    return sprintf('%.2f '.$symbols[$exp], ($bytes/pow(1024, floor($exp))));
}
function states(){
    $result = query("SELECT * FROM `states`");
    $select = "<select name='state'>";
    foreach ($result as $pr){
        $select = $select."<option value='".$pr['state_id']."'>".$pr['state_name']."</option>";
    }
    $select = $select."</select>";
    return $select;
}
function citiesSelect($id = ''){
    $result = query("SELECT * FROM `cities`");
    $select = "<select name='city'>";
    foreach ($result as $pr){
        if($pr['city_id'] == $id){
            $select = $select."<option value='".$pr['city_id']."' selected>".$pr['city_name']."</option>";
        }else{
            $select = $select."<option value='".$pr['city_id']."'>".$pr['city_name']."</option>";
        }

    }
    $select = $select."</select>";
    return $select;
}
function sections_select($id = ''){
    $result = query("SELECT * FROM `sections` WHERE `delete` = 0");
    $select = "<select name='section'>";
    foreach ($result as $pr){
        if($pr['section_id'] == $id){
            $select = $select."<option value='".$pr['section_id']."' selected>".$pr['section_name']."</option>";
        }else{
            $select = $select."<option value='".$pr['section_id']."'>".$pr['section_name']."</option>";
        }

    }
    $select = $select."</select>";
    return $select;
}
function shop_category_select($id = ''){
    $sections = query("SELECT * FROM `sections` WHERE !`delete`");
    $select_category = "<select name='shop_category'>";
    foreach ($sections as $pr){
        $select_category = $select_category."<option disabled>&nbsp; - ".$pr['section_name']."</option>";
        $category = query("SELECT * FROM `sections_meta` WHERE `section_meta_key` = 'shop_category' AND `section_meta_section_id` = ".$pr['section_id']);
        foreach ($category as $pri){
            $select_category =  $select_category."<option value='".$pri['section_meta_id']."'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -- ".$pri['section_meta_value']."</option>";
        }
    }
    return $select_category."</select>";

}

function createCode(){
    $al = array_merge(range('A','Z') , range(0,9));
    $cap = "";
    for($i=0;$i<=6;$i++)
        $cap .= $al[rand(0,35)];
    $cap = mb_strtolower($cap);
    global $db;
    $result = query("SELECT * FROM `discounts` WHERE `discount_code` = '".$cap."' AND `discount_active`");
    if($result->rowCount() >= 1 ){
        return createCode();
    }else{
        return $cap;
    }
}
?>