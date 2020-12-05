<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 5/31/2017
 * Time: 7:31 PM
 */
require_once 'api.functions.php';
$type = preg_replace('/\s+/', '', $type);
if(check_set_empty(@$type)) {
    switch ($type) {
        
        case 'cities':
            
            $result = query("SELECT `city_id` as cid,`city_name` as cn FROM `cities` WHERE `city_active`");
            if($result->rowCount() > 0 ){
                $array = array();
                foreach ($result as $pr){
                    array_push($array,array(
                        "c" => $pr['cid'],
                        "n" => $pr['cn']
                    ));
                }
                echo_json($array);
            }else{
                echo response("CLOSE");
            }

            break;
        case "getHome":

            if(check_set_empty(@$cid)){
                $cid = intval($cid);
                $lastHomeUpdate = query("SELECT `city_meta_value` as city_meta_date FROM `cities_meta` WHERE `city_meta_key` = 'homeData' AND `city_meta_active` AND `city_meta_city_id` = ".$cid)->fetch()['city_meta_date'];
                if ($lastHomeUpdate == ""){
                    echo response("CLOSE");
                }else{
                    echo base64_decode($lastHomeUpdate);
                }
            }
            break;
        case "getSections":
            if(check_set_empty(@$cid)){
                $cid = intval($cid);
                $sections = query("SELECT `section_meta_section_id` as sid FROM `sections_meta` WHERE `meta_active` AND `section_meta_value` = '".$cid."' AND `section_meta_key` = 'city'");
                if ($sections->rowCount() >= 1){
                    $array = array();
                    foreach ($sections as $pr){
                        $sid = intval($pr['sid']);
                        if(query("SELECT `active` as active FROM `sections` WHERE !`delete` AND `section_id` = ".$sid)->fetch()['active']){
                            $name = query("SELECT `section_name` as name FROM `sections` WHERE `section_id` = ".$sid)->fetch()['name'];
                            $banner = query("SELECT `section_meta_value` as banner FROM `sections_meta` WHERE `section_meta_section_id` = '".$sid."' AND `meta_active` AND `section_meta_key` = 'banner' LIMIT 1 ")->fetch()['banner'];

                            $open_type = query("SELECT `shop_id` FROM `shops` WHERE `shop_section_id` = '".$sid."' AND `shop_city_id` = ".$cid);
                            if($open_type->rowCount() > 1){
                                $open_type = "section";
                            }else if($open_type->rowCount() == 1){
                                $open_type = $open_type->fetch()['shop_id'];
                            }else{
                                $open_type = "section";
                            }
                            array_push($array,array(
                                "sid" =>  $sid ,
                                "banner" => ($banner != NULL) ? UPLOADS.$banner : "no",
                                "open" => $open_type
                            ));
                        }
                        
                    }
                    echo json_encode($array);
                }else{
                    echo response("CLOSE");
                }
            }
            break;
        case "getSectionsCategory":
            if(check_set_empty(@$sid) && check_set_empty(@$cid)){
                $sid = intval($sid);
                $cid = intval($cid);
                $categories = query("SELECT * FROM `sections_meta` WHERE `meta_active` AND `section_meta_section_id` = '".$sid."' AND `section_meta_key` = 'shop_category'");
                if ($categories->rowCount() >= 1){
                    $array = array();
                    foreach ($categories as $key) {
                        array_push($array,array(
                            "cid" =>  $key['section_meta_id'] ,
                            "name" => $key['section_meta_value']
                        ));
                    }
                    
                    /* foreach ($sections as $pr){
                        $cid = intval($pr['section_meta_id']);
                        $name = query("SELECT `section_meta_value` as name FROM `sections_meta` WHERE `section_meta_section_id` = '".$cid."' AND `meta_active` ")->fetch()['name'];

                    } */
                    echo json_encode($array,JSON_UNESCAPED_UNICODE);
                }else{
                    $shops = query("SELECT `shop_id`,`shop_name`,`shop_address` FROM `shops` WHERE `shop_city_id` = '".$cid."' AND `shop_section_id` = '".$sid."' ORDER BY `shop_id` DESC LIMIT 15");
                    $array = array();
                    foreach ($shops as $pr){
                        $shop_id = intval($pr['shop_id']);
                        $logo = query("SELECT `shop_meta_value` as logo FROM `shops_meta` WHERE `shop_meta_shop_id` = '".$shop_id."' AND `shop_meta_active` AND `shop_meta_key` = 'logo' LIMIT 1")->fetch()['logo'];
                        array_push($array,array(
                            "sid" =>  $shop_id ,
                            "n" =>  $pr['shop_name'] ,
                            "a" =>  $pr['shop_address'] ,
                            "o" => $pr['shop_active']
                        ));
                    }
                    echo json_encode($array,JSON_UNESCAPED_UNICODE);
                }
            }
            break;
    }
}