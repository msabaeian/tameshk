<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 6/4/2017
 * Time: 12:16 AM
 */

function discountCheck($cid,$sid,$username,$password,$code,$echo = false){
    if ( isUser($username,$password)){
        if(isApprove($username,$password)){
            $cid = intval($cid);
            $sid = intval($sid);
            $uid =  isUser($username,$password);
            if(isShop($sid)){
                if(isShopActive($sid)){
                    $code = cleanInput(mb_strtolower($code));
                    $result = query("SELECT * FROM `discounts` WHERE `discount_code` = '".$code."' AND `discount_active`");
                    if($result->rowCount() == 1){
                        $result = $result->fetch();
                        if(intval($result['discount_total_use']) != 0){
                            if($result['discount_city'] && intval($result['discount_city']) != $cid){
                                return response("NOT_FOR_THIS_CITY");
                            }else{
                                if($result['discount_shop'] && intval($result['discount_shop']) != $sid){
                                    return response("NOT_FOR_THIS_SHOP");
                                }else{
                                    if($result['discount_uid'] && intval($result['discount_uid']) != $uid){
                                        return response("NOT_FOR_THIS_USER");
                                    }else{
                                        if(round(abs(strtotime($result['discount_delete_date']) - strtotime(date("Y-m-d H:i:s",time()))) / 60,2) > 0 ){
                                            if(intval($result['discount_percent']) == -1){ // پست رایگان
                                                if($echo) return -1;
                                                return -1;
                                            }else{ // مبلغ
                                                if($echo) return 1;
                                                return 1;
                                            }
                                        }else{
                                            return response("TIME_IS_LEFT");
                                        }
                                    }
                                }
                            }
                        }else{
                            return response("END_OF_USE_DISCOUNT");
                        }
                    }else{
                        return response("DISCOUNT_NOT_FOUND");
                    }
                }else{
                    return response("SHOP_CLOSED");
                }
            }else{
                return response("NOT_FOUND");
            }
        }else{
            return response("USER_LOCK");
        }

    }else{ // Check user
        return response("USER_NOT_FOUND");
    }
}

function isUser($username,$password){
    $result = query("SELECT * FROM `users` WHERE `user_login` = '".username($username)."' AND `user_pass` = '".password($password)."'");
    if ( $result->rowCount() == 1){
        return intval($result->fetch()['users_ID']);
    }
    return 0;

}

function isApprove($username,$password){
    $result = query("SELECT * FROM `users` WHERE `user_login` = '".username($username)."' AND `user_pass` = '".password($password)."'");
    if ( $result->rowCount() == 1){
        return intval($result->fetch()['user_approve']);
    }
    return 0;

}

function isShop($sid){
    $sid = intval($sid);
    $shop = query("SELECT `shop_id` FROM `shops` WHERE `shop_id` = ".$sid);
    if($shop->rowCount() == 1){
        return $shop->fetch()['shop_id'];
    }
    return 0;
}

function isShopActive($sid){
    return query("SELECT `shop_active` FROM `shops` WHERE `shop_id` = ".intval($sid))->fetch()['shop_active'];
}

function isProductShop($pid,$sid){
    $sid = intval($sid); $pid = intval($pid);
    $result = query("SELECT `product_exist`,`product_price`,`product_off` FROM `products` WHERE `product_id` = '".$pid."' AND `product_shop` = '".$sid."' AND !`product_delete`");
    if($result->rowCount() == 1) {
        $result = $result->fetch();
        if($result['product_exist']) return $result;
        return false;
    };
    return false;
}

function GetDrivingDistance($lat1_lat2, $long1_long2)
{
    $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$lat1_lat2."&destinations=".$long1_long2."&mode=driving&language=pl-PL";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $response = curl_exec($ch);
    curl_close($ch);
    $response_a = json_decode($response, true);
    $dist = $response_a['rows'][0]['elements'][0]['distance']['text'];
    //$time = $response_a['rows'][0]['elements'][0]['duration']['text'];
    $dist = str_replace(",",".",$dist);
    return intval($dist); //array('distance' => $dist, 'time' => $time);
}


?>