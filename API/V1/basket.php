<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 6/2/2017
 * Time: 12:05 AM
 */

require_once 'api.functions.php';
require_once 'api.order.function.php';
if(check_set_empty(@$type)) {
    switch ($type) {
        case "discount_check":
            if(check_set_empty(@$cid) && check_set_empty(@$sid) && check_set_empty(@$username) && check_set_empty(@$password)&& check_set_empty(@$code) ) {
                $code = preg_replace('/\s+/', '', cleanInput($code));
                echo discountCheck($cid,$sid,$username,$password,$code,true);
            }
            break;

        case "create_order":
            if(check_set_empty(@$sid) && check_set_empty(@$username) && check_set_empty(@$password)&& check_set_empty(@$items)&& check_set_empty(@$discount,false) && check_set_empty(@$description,false) && check_set_empty(@$address)&& check_set_empty(@$gps)){
                $items =  $_POST['items'];
                $items = json_decode($items,true);
                
                $sid = intval($sid); $username = username($username); $discount = cleanInput($discount); $address = cleanInput($address); $gps = cleanInput($gps);
                $gps = ($gps == "1" || $gps ==1) ? $gps = 0 : cleanInput($gps);
                $user = isUser($username,$password);
                if($user){
                    
                    if(isShop($sid)){
                        if(isShopActive($sid)){
                            
                            $order['order_user_id'] = $user;
                            $order['order_shop_id'] = $sid;
                            $order['order_date'] = get_date();
                            require_once ('../../module/php/jdf.php');
                            $order['order_jdate'] = jdate("Y-m-d H:i:s",time());
                            $order['order_address'] = $address;
                            $order['order_gps'] = $gps;
                            $order['order_status'] = 1;
                            $order['order_description'] = $description;

                            $al = array_merge(range('A','Z') , range(0,9));
                            $cap = "";
                            for($i=0;$i<=3;$i++)
                                $cap .= $al[rand(0,35)];
                            $order['order_code'] = date("YmdHi").$cap;
                            query(queryInsert('orders',$order));
                            global $db;
                            $order_id = query("SELECT `order_id` AS oid FROM `orders` WHERE `order_code` = '".$order['order_code']."'")->fetch()['oid'];
                            $order_price = 0;
                            $order_total_items = 0;
                            $order_total = 0;
                            $delivery_total = 0;
                            $server_item = array();
                            foreach ($items as $item){
                                $product = isProductShop($item['pId'],$sid);
                                if($product){
                                    array_push($server_item,$item);
                                    $order_item['orders_item_order_id'] = $order_id;
                                    $order_item['orders_item_product_id'] = $item['pId'];
                                    $order_item['orders_item_product_count'] = $item['pCount'];
                                    $order_item['orders_item_price'] = $product['product_price'];
                                    $order_item['orders_item_final_price'] = $product['product_price'];
                                    $order_item['orders_item_total'] = $product['product_price'];

                                    if((intval($product['product_off']) < intval($product['product_price'])) && intval($product['product_off']) !=0 ){
                                        $order_item['orders_item_final_price'] = $product['product_off'];
                                    }
                                    $order_item['orders_item_total'] = intval($order_item['orders_item_final_price'] * $item['pCount']);
                                    query(queryInsert('orders_item',$order_item));
                                    $order_price += intval($product['product_price'] * $item['pCount']);
                                    $order_total_items += intval($order_item['orders_item_total']);
                                }else{
                                    array_push($server_item,"NOT_FOUND_PRODUCT_EXIST");
                                }
                            }

                            if($gps==0){
                                $delivery_total = 0;
                            }else{
                                $shop = query("SELECT `delivery_cost`,`delivery_free`,`delivery_add_cost_km`,`delivery_add_cost_price`,`shop_lat_lng` FROM `shops` WHERE `shop_id` = ".$sid);
                                $shop = $shop->fetch();
                                $delivery_total = $shop['delivery_cost'];
                                if($shop['delivery_add_cost_km']){
                                    $distance = GetDrivingDistance($shop['shop_lat_lng'],$gps);
                                    if($distance > 2){
                                        $distance = $distance -2;
                                        $delivery_total += ( intval($distance/intval($shop['delivery_add_cost_km'])) * intval($shop['delivery_add_cost_price']) );
                                    }
                                }
                            }
                            $update_order['order_delivery'] = $delivery_total;


                            $city_id = query("SELECT `shop_city_id` as cid FROM `shops` WHERE `shop_id` = ".$sid)->fetch()['cid'];
                                $discount_check = discountCheck($city_id,$sid,$username,$password,$discount,false);
                                if($discount_check == 1 && ($order_price == $order_total_items)){
                                    $discount = query("SELECT `discount_percent` as discount_price,`discount_id` as did,`discount_min` as dmin  FROM `discounts` WHERE `discount_code` = '".$discount."' AND `discount_active`");
                                   $discount = $discount->fetch();
                                   $discount_use['benefit'] = 0;
                                    if($order_total_items >= intval($discount['dmin'])){
                                        $order_total_items -= intval($discount['discount_price']);

                                        $discount_use['discount_use_did'] = $discount['discount_id'];
                                        $discount_use['discount_use_uid'] = $user;
                                        $discount_use['discount_use_order_id'] = $order_id;
                                        $discount_use['discount_use_date'] = get_date();
                                        require_once ('../../module/php/jdf.php');
                                        $discount_use['discount_use_jdate'] = jdate("Y-m-d H:i:s",time());
                                        $discount_use['benefit'] = $discount['discount_price'];
                                        query(queryInsert('discounts_use',$discount_use));
                                        $update_order['order_discount_id'] = $discount['did'];
                                    }
                                }else if($discount_check == -1){
                                    $discount_use['benefit'] = $delivery_total;
                                    $delivery_total = 0;
                                }else{
                                    $discount_use['benefit'] = 0;
                                }
                            

                            $update_order['order_price'] = $order_price;
                            $update_order['order_final_price'] = $order_total_items;
                            $update_order['order_total'] = intval($order_total_items + $delivery_total);
                            query(queryUpdate('orders',$update_order," WHERE `order_id` = ".$order_id));

                            echo json_encode(array(
                                "delivery" =>  $update_order['order_delivery'],
                                "price" => $order_price ,
                                "final" => $order_total_items ,
                                "total" => $update_order['order_total'],
                                "discount" => @$discount_use['benefit'],
                                "orderCode" => $order['order_code'] ,
                                "user_balance" => query("SELECT `user_balance` as b FROM `users` WHERE `users_ID` = ".$user)->fetch()['b']
                            ));
                        }else{
                            return response("SHOP_CLOSED");
                        }
                    }else{
                        return response("SHOP_NOT_FOUND");
                    }
                }else{
                    return response("USER_NOT_FOUND");
                }
            }
            break;
    }
}
?>