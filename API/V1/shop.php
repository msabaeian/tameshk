<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 6/1/2017
 * Time: 5:03 PM
 */
require_once 'api.functions.php';

if(check_set_empty(@$type)) {
    switch ($type) {
        case "sectionShopsCategory":
            if(check_set_empty(@$sid) ) {
                $array = array();
                foreach (query("SELECT `section_meta_id` as cid,`section_meta_value` as cname FROM `sections_meta` WHERE `section_meta_section_id` = '" . $sid . "' AND `section_meta_key` = 'shop_category' AND `meta_active`") as $category){
                    array_push($array, array(
                        "cat_id" => $category['cid'],
                        "n" => $category['cname'],
                    ));
                }
                echo json_encode($array, JSON_UNESCAPED_UNICODE);
            }
            break;
        case "getShopsCategory":
            if(check_set_empty(@$sid)&& check_set_empty(@$cid) && check_set_empty(@$catid) && check_set_empty(@$offset,false) ) {
                $sid = intval($sid);
                $cid = intval($cid);
                $catid = intval($catid);
                $offset = (@$offset > 0 ) ? intval($offset) : 0;

                $shops = query("SELECT `shop_id`,`shop_name`,`shop_address`,`shop_active` FROM `shops` WHERE `shop_city_id` = '" . $cid . "' AND `shop_section_id` = '" . $sid . "' AND `shop_category_id` = '" . $catid . "' ORDER BY `shop_id` LIMIT 25 OFFSET " . $offset);
                $array = array();
                foreach ($shops as $pr) {
                    $shop_id = intval($pr['shop_id']);
                    $logo = UPLOADS.query("SELECT `shop_meta_value` as logo FROM `shops_meta` WHERE `shop_meta_shop_id` = '" . $shop_id . "' AND `shop_meta_active` AND `shop_meta_key` = 'logo' LIMIT 1")->fetch()['logo'];
                    array_push($array, array(
                        "s" => $shop_id,
                        "n" => $pr['shop_name'],
                        "a" => $pr['shop_address'],
                        "o" => $pr['shop_active'],
                        "l" => $logo
                    ));
                }
                echo json_encode($array, JSON_UNESCAPED_UNICODE);
            }
                break;
        case "shop":
            if(check_set_empty(@$sid)){
                $shop_id = intval($sid);
                $shop = query("SELECT * FROM `shops` WHERE `shop_id` = '".$shop_id."'");
                if($shop->rowCount() ==1 ){
                    $shop = $shop->fetch();

                    $logo = UPLOADS.query("SELECT `shop_meta_value` as logo FROM `shops_meta` WHERE `shop_meta_shop_id` = '" . $shop_id . "' AND `shop_meta_active` AND `shop_meta_key` = 'logo' LIMIT 1")->fetch()['logo'];

                    $images = array();
                    foreach (query("SELECT * FROM `shops_meta` WHERE `shop_meta_key` = 'image' AND `shop_meta_shop_id` = '".$shop_id."' AND `shop_meta_active` != 0") as $pr){
                        array_push($images,array("i" => UPLOADS.$pr['shop_meta_value']));
                    }

                    // SHOP CATEGORIES
                    $cat = array();
                    foreach ( query("SELECT `relation_category_id` as rci FROM `products_relation` WHERE `relation_shop_id` = '".$shop_id."'") as $cat_id){
                        array_push($cat,$cat_id['rci']);
                    }
                    $cat = array_unique($cat);

                    $array = array();
                    foreach ($cat as $pr){
                        $smid = intval($pr);
                        $name = query("SELECT `section_meta_value` as name FROM `sections_meta` WHERE `section_meta_id` = '".$smid."' AND `meta_active` ")->fetch()['name'];
                        array_push($array,array(
                            "c" =>  $smid ,
                            "n" => $name
                        ));
                    }
                   // echo json_encode($array);

                    echo json_encode(array(
                        "n" => $shop['shop_name'] ,
                        "lo" => $logo ,
                        "a" => $shop['shop_address'] ,
                        "ll" => $shop['shop_lat_lng'] ,
                        "dc" => $shop['delivery_cost'] ,
                        "df" => $shop['delivery_free'] ,
                        "dack" => $shop['delivery_add_cost_km'] ,
                        "dacp" => $shop['delivery_add_cost_price'],
                        "sm" => $shop['shop_min'] ,
                        "ac" => $shop['shop_active'] ,
                        "im" => $images ,
                        "c" => $array
                    ),JSON_UNESCAPED_UNICODE);
                }else{
                    response("SHOP_NOT_FOUND");
                }
            }
            break;
        case "product":
            if(check_set_empty(@$pid)){
                $product_id = intval($pid);
                $product = query("SELECT * FROM `products` WHERE `product_id` =  '".$product_id."'");
                if($product->rowCount() ==1 ){
                    $like = 0;
                    if(check_set_empty(@$username) && check_set_empty(@$password)){
                        $result = query("SELECT * FROM `users` WHERE `user_login` = '".username($username)."' AND `user_pass` = '".password($password)."'");
                        if ( $result->rowCount() == 1) {
                            $result = $result->fetch();
                            $fetch = query("SELECT * FROM `likes` WHERE `like_user_id` = '".intval($result['users_ID'])."' AND `like_product_id` = ".$product_id);
                            if($fetch->rowCount() > 0) $like =1 ;
                        }
                    }

                    $product = $product->fetch();
                    $images = array();
                    foreach (query("SELECT * FROM `products_meta` WHERE `product_meta_key` = 'image' AND `product_id` = '".$product_id."' AND `product_meta_active` != 0") as $pr){
                        array_push($images,array("i" => UPLOADS.$pr['product_meta_value']));
                    }
                    $shop = query("SELECT `shop_name` as name,`shop_active` as sactive,`shop_min` as shop_min FROM `shops` WHERE `shop_id` = ".intval($product['product_shop']));
                    $shop = $shop->fetch();
                    echo json_encode(array(
                        "n" => $product['product_name'] ,
                        "p" => $product['product_price'] ,
                        "np" => (intval($product['product_off'])==0) ? $product['product_price'] : $product['product_off'],
                        "sid" => $product['product_shop'] ,
                        "sn" => $shop['name'] ,
                        "sa" => $shop['sactive'] ,
                        "t" => $product['product_description'] ,
                        "sm" => $shop['shop_min'] ,
                        "d" => $product['product_delivery_time'] , 
                        "e" => $product['product_exist'] ,
                        "l" =>  $like,
                        "ul" => $product['product_likes'],
                        "im" =>  $images //implode("<joda>",$images)
                    ),JSON_UNESCAPED_UNICODE);
                }else{
                    response("NOT_FOUND");
                }
            }
            break;
        case "categoryProduct":
            if(check_set_empty(@$sid) && check_set_empty(@$shid) && check_set_empty(@$offset,FALSE)){
                $offset = intval($offset);
                $shop_id = intval($shid);
                $cat_id = intval($sid);

                $array = array();
                foreach (query("SELECT `relation_product_id` as pid  FROM `products_relation`  WHERE `relation_shop_id` = '".$shop_id."' AND `relation_category_id` = '".$cat_id."' ORDER BY `relation_id` DESC LIMIT 10 OFFSET ".$offset) as $product){
                    $product_detail = query("SELECT * FROM `products` WHERE !`product_delete` AND `product_id` = ".$product['pid']);
                    if ($product_detail->rowCount() >= 1 ){
                        $product_detail = $product_detail->fetch();
                        $images = array();
                        foreach (query("SELECT * FROM `products_meta` WHERE `product_meta_key` = 'image' AND `product_id` = '".intval($product_detail['product_id'])."' AND `product_meta_active` != 0 LIMIT 1") as $pr){
                            array_push($images,UPLOADS.$pr['product_meta_value']);
                        }
                        array_push($array,array(
                            "pid" => $product_detail['product_id'] ,
                            "n" => $product_detail['product_name'] ,
                            "p" => $product_detail['product_price'] ,
                            "np" => (intval($product_detail['product_off'])==0) ? $product_detail['product_price'] : $product_detail['product_off'],
                            "l" => $product_detail['product_likes'] ,
                            "t" => $product_detail['product_delivery_time'] ,
                            //"d" => $product_detail['product_description'] ,
                            "e" => $product_detail['product_exist'] ,
                            "i" => implode("<joda>",$images)
                        ));
                    }
                }
                echo json_encode($array,JSON_UNESCAPED_UNICODE);
            }
            break;
        case "ProductsOrderByLikes":
            if(check_set_empty(@$cid) ){
                $city_id = intval($cid);
                $array = array();
                foreach (query("SELECT * FROM `shops` as shops LEFT JOIN `products` as products ON products.product_shop = shops.shop_id WHERE shops.shop_city_id = '".$city_id."' ORDER BY `product_likes` DESC LIMIT 10 ") as $product){
                        $image = UPLOADS.query("SELECT * FROM `products_meta` WHERE `product_meta_key` = 'image' AND `product_id` = '".$product_id."' AND `product_meta_active` != 0 LIMIT 1")->fetch()['product_meta_value'];
                        array_push($array,array(
                            "p" => $product_detail['product_id'] ,
                            "n" => $product_detail['product_name'] ,
                            //"p" => $product_detail['product_price'] ,
                            //"np" => (intval($product_detail['product_off'])==0) ? $product_detail['product_price'] : $product_detail['product_off'],
                            "l" => $product_detail['product_likes'] ,
                            //"t" => $product_detail['product_delivery_time'] ,
                            "i" => $image
                        ));
                }
                echo json_encode($array,JSON_UNESCAPED_UNICODE);
            }
            break;
            
        case "search":
            if(check_set_empty(@$cid) && check_set_empty(@$s) && check_set_empty(@$offset,FALSE)){
                $offset = intval($offset);
                $cid = intval($cid);
                $s = cleanInput(trim(preg_replace('/\s+/', ' ', $s)));
                $array = array();
                foreach (query("SELECT * FROM `products`  as products
                                LEFT JOIN shops as shops ON products.product_shop = shops.shop_id
                                WHERE products.`product_active` AND !products.`product_delete` AND shops.shop_city_id = '".$cid."' AND products.product_name LIKE '%".$s."%' 
                                ORDER BY product_id LIMIT 20 OFFSET  ".$offset) as $product){
                        $image = UPLOADS.query("SELECT * FROM `products_meta` WHERE `product_meta_key` = 'image' AND `product_id` = '".intval($product['product_id'])."' AND `product_meta_active` != 0 LIMIT 1")->fetch()['product_meta_value'];

                        array_push($array,array(
                            "pid" => $product['product_id'] ,
                            "n" => $product['product_name'] ,
                            "p" => $product['product_price'] ,
                            "np" => (intval($product['product_off'])==0) ? $product['product_price'] : $product['product_off'],
                            "l" => $product['product_likes'] ,
                            "t" => $product['product_delivery_time'] ,
                            "e" => $product['product_exist'] ,
                            "i" => $image
                        ));

                
                }
                echo json_encode($array,JSON_UNESCAPED_UNICODE);
            }
            break;
    }
}