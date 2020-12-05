<?php
/**
 * Created by Cpanel.
 * User: Mohammad
 * Date: 6/30/2017
 * Time: 9:46 PM
 */
require_once 'api.functions.php';

if(check_set_empty(@$type)){
    switch ($type){
        case 'login':
            if(check_set_empty(@$username) && check_set_empty(@$password)){
                $result = query("SELECT * FROM `users` WHERE `user_login` = '".username($username)."' AND `user_pass` = '".password($password)."'");
                if ( $result->rowCount() == 1){
                    $fetch = $result->fetch();
                    $user_approve = intval($fetch['user_approve']);
                    if($user_approve == 1){
                        $shop = query("SELECT `shop_id`,`shop_name`,`shop_active`,`percent` FROM `shops` WHERE `shop_admin` = ".$fetch['users_ID']);
                        if($shop->rowCount() == 1){
                            $shop = $shop->fetch();
                            echo_json(array(
                                "udn" => cleanInput($fetch['user_display_name']),
                                "sn" => $shop['shop_name'] ,
                                "si" => $shop['shop_id'] , 
                                "sa" => $shop['shop_active'] , 
                                "p" => $shop['percent'] , 
                                "uc" => query("SELECT `users_meta_value` as card FROM `users_meta` WHERE `users_meta_key` = 'card' AND `users_meta_user_id` = ".$fetch['users_ID'])->fetch()['card'],
                                "l" => UPLOADS.query("SELECT `shop_meta_value` as logo FROM `shops_meta` WHERE `shop_meta_shop_id` = '" . $shop['shop_id'] . "' AND `shop_meta_active` AND `shop_meta_key` = 'logo' LIMIT 1")->fetch()['logo']
                            ));
                        }else{
                            echo response("NOT_ANY_SHOP_ADMIN");
                        }
                        
                    }else{
                        echo response("USER_LOCK");
                    }

                }else{ // Check user
                    echo response("USER_NOT_FOUND");
                }
            }
            break;
        case 'onOff':
            if(check_set_empty(@$username) && check_set_empty(@$password) && check_set_empty(@$do,false) && check_set_empty(@$sid)){
                $result = query("SELECT * FROM `users` WHERE `user_login` = '".username($username)."' AND `user_pass` = '".password($password)."'");
                if ( $result->rowCount() == 1){
                    $fetch = $result->fetch();
                    $user_approve = intval($fetch['user_approve']);
                    if($user_approve == 1){
                        
                        $sid = intval($sid);
                        $do = (intval($do) == 1) ? 1 : 0;
                        query("UPDATE `shops` SET `shop_active`=$do WHERE `shop_id` = $sid AND `shop_admin` = ".$fetch['users_ID']);
                        echo response("UPDATED");
                        
                    }
                }
            }
            break;
        case 'card':
            if(check_set_empty(@$username) && check_set_empty(@$password) && check_set_empty(@$card)){
                $result = query("SELECT * FROM `users` WHERE `user_login` = '".username($username)."' AND `user_pass` = '".password($password)."'");
                if ( $result->rowCount() == 1){
                    $fetch = $result->fetch();
                    $user_approve = intval($fetch['user_approve']);
                    if($user_approve == 1){
                        
                        $card = intval($card);
                        query("DELETE FROM `users_meta` WHERE `users_meta_key` = 'card' AND `users_meta_user_id` = ".$fetch['users_ID']);
                        $insert['users_meta_user_id'] = $fetch['users_ID'];
                        $insert['users_meta_key'] = 'card';
                        $insert['users_meta_value'] = $card;
                        query(queryInsert('users_meta',$insert));
                        echo response("UPDATED");
                        
                    }
                }
            }
            break;
        case 'post':
             if(check_set_empty(@$username) && check_set_empty(@$password) && check_set_empty(@$offset,false) && check_set_empty(@$status)){
                 
                $result = query("SELECT * FROM `users` WHERE `user_login` = '".username($username)."' AND `user_pass` = '".password($password)."'");
                if ( $result->rowCount() == 1){
                    $fetch = $result->fetch();
                    $user_approve = intval($fetch['user_approve']);
                    if($user_approve == 1){
                        $shop = query("SELECT `shop_id` FROM `shops` WHERE `shop_admin` = ".$fetch['users_ID']);
                        if($shop->rowCount() == 1){
                            $shop = $shop->fetch()['shop_id'];
                            $offset = intval($offset);
                            $status = intval($status);
                            $sql = "SELECT * FROM `orders` as orders
                                	LEFT JOIN users AS users ON users.`users_ID` = orders.`order_user_id`
                                    WHERE orders.`order_shop_id` = $shop AND orders.`order_status` = $status
                                    ORDER BY orders.order_id LIMIT 10 OFFSET $offset";
                            $array = array();
                            foreach(query($sql) as $pr){
                                $products = "";
                                $sql_items = "SELECT `orders_item_product_count`,`product_name` FROM `orders_item` as orders_item
                                LEFT JOIN products as products ON products.product_id  = orders_item.orders_item_product_id
                                WHERE `orders_item_order_id` = ".$pr['order_id'];
                                foreach(query($sql_items) as $item){
                                    $products .= $item['product_name']. " - X ".$item['orders_item_product_count']." \n";
                                }
                                array_push($array,array(
                                    "oc" => $pr['order_code'] , 
                                    "op" => $pr['order_price'] , 
                                    "ot" => $pr['order_total'] , 
                                    "od" => $pr['order_delivery'] , 
                                    "oa" => $pr['order_address'] , 
                                    "d" => $pr['order_description'] , 
                                    "og" => $pr['order_gps'] , 
                                    "odt" => $pr['order_jdate'] , 
                                    "un" => $pr['user_display_name'] , 
                                    "up" => $pr['user_phone'] ,
                                    "a" => UPLOADS."avatars/avatar_square_blue.png" , 
                                    "i" => $products
                                    
                                ));
                            }
                            echo_json($array);
                        }else{
                            echo response("NOT_ANY_SHOP_ADMIN");
                        }
                        
                    }
                }
            }
            break;
        case 'postUpdate':
            if(check_set_empty(@$username) && check_set_empty(@$password) && check_set_empty(@$order_code) && check_set_empty(@$do)){
                $result = query("SELECT * FROM `users` WHERE `user_login` = '".username($username)."' AND `user_pass` = '".password($password)."'");
                if ( $result->rowCount() == 1){
                    $fetch = $result->fetch();
                    $user_approve = intval($fetch['user_approve']);
                    if($user_approve == 1){
                        $shop = query("SELECT `shop_id` FROM `shops` WHERE `shop_admin` = ".$fetch['users_ID']);
                        if($shop->rowCount() == 1){
                            $shop = $shop->fetch()['shop_id'];
                            //"si" => $shop['shop_id']
                            $order_code = preg_replace('/\s+/', '', cleanInput($order_code));
                            $do = intval($do);
                            
                            
                            if($do == 3){
                                query("UPDATE `orders` SET `order_status`= 3 WHERE `order_code` = '".$order_code."'");
                                $user_phone = query("SELECT `user_phone` FROM `orders` as orders  LEFT JOIN users as users on users.users_ID = orders.order_user_id WHERE `order_code` = '".$order_code."'")->fetch()['user_phone'];
                                sendSms($user_phone,'orderAccept',$order_code);
                                echo response("UPDATED");
                            }else if($do == 4){
                                query("UPDATE `orders` SET `order_status`= 4 WHERE `order_code` = '".$order_code."'");
                                $user_phone = query("SELECT `user_phone` FROM `orders` as orders  LEFT JOIN users as users on users.users_ID = orders.order_user_id WHERE `order_code` = '".$order_code."'")->fetch()['user_phone'];
                                sendSms($user_phone,'orderSend',$order_code);
                                echo response("UPDATED");
                            }else if($do == 7){
                                query("UPDATE `orders` SET `order_status`= 7 WHERE `order_code` = '".$order_code."'");
                                $total = query("SELECT `order_user_id` , `order_total `FROM `orders` WHERE `order_code` = '".$order_code."'"); $total = $total->fetch();
                                query("UPDATE `users` SET `user_balance`=`user_balance`+'".$total['order_total']."'  WHERE `users_ID` = ".$total['order_user_id']);
                                $user_phone = query("SELECT `user_phone` FROM `orders` as orders  LEFT JOIN users as users on users.users_ID = orders.order_user_id WHERE `order_code` = '".$order_code."'")->fetch()['user_phone'];
                                sendSms($user_phone,'orderReject',$order_code);
                                echo response("UPDATED");
                            }
                            
                            
                        }else{
                            echo response("NOT_ANY_SHOP_ADMIN");
                        }
                        
                    }else{
                        echo response("USER_LOCK");
                    }

                }else{ // Check user
                    echo response("USER_NOT_FOUND");
                }
            }
            break;
        case 'ballance':
             if(check_set_empty(@$username) && check_set_empty(@$password)){
                 
                $result = query("SELECT * FROM `users` WHERE `user_login` = '".username($username)."' AND `user_pass` = '".password($password)."'");
                if ( $result->rowCount() == 1){
                    $fetch = $result->fetch();
                    $user_approve = intval($fetch['user_approve']);
                    if($user_approve == 1){
                        $shop = query("SELECT `shop_id` FROM `shops` WHERE `shop_admin` = ".$fetch['users_ID']);
                        if($shop->rowCount() == 1){
                            $shop = $shop->fetch()['shop_id'];
                            $price = query("SELECT SUM(`order_total`) as total , SUM(`order_delivery`) as delivery FROM `orders` WHERE `order_status` = 4 AND `order_shop_id` = ".$shop); $price = $price->fetch();
                            $cash_outs = query("SELECT SUM(`shop_meta_value`) AS cash_out FROM `shops_meta` WHERE `shop_meta_key` = 'cash_out_product' AND `shop_meta_shop_id` = ".$shop); $cash_outs = intval($cash_outs->fetch()['cash_out']);
                            $cash_out_delivery = query("SELECT SUM(`shop_meta_value`) AS cash_out_delivery FROM `shops_meta` WHERE `shop_meta_key` = 'cash_out_delivery' AND `shop_meta_shop_id` = ".$shop); $cash_out_delivery = intval($cash_out_delivery->fetch()['cash_out_delivery']);
                            $cash_out_profit = query("SELECT SUM(`shop_meta_value`) AS cash_out_profit FROM `shops_meta` WHERE `shop_meta_key` = 'cash_out_profit' AND `shop_meta_shop_id` = ".$shop); $cash_out_profit = intval($cash_out_profit->fetch()['cash_out_profit']);
                            $cash_outs = ($cash_outs == NULL) ? 0 : $cash_outs;
                            $cash_out_delivery = ($cash_out_delivery == NULL) ? 0 : $cash_out_delivery;
                            $cash_out_profit = ($cash_out_profit == NULL) ? 0 : $cash_out_profit;
                            echo_json(array(
                                "t" => $price['total'] , // کل فروش
                                "d" => $price['delivery'] ,  // کل هزینه ارسالات
                                "cd" => $cash_out_delivery , // تسویه شده ارسالات
                                "cp" => $cash_out_profit , // سود
                                "c" => $cash_outs , // تسویه شده محصولات
                                "b" => $price['total'] - ($price['delivery']+$cash_outs+$cash_out_profit) , // موجودی حاضر
                                "bd" => $price['delivery'] - $cash_out_delivery // موجودی حاضر ارسالات
                            ));
                        }else{
                            echo response("NOT_ANY_SHOP_ADMIN");
                        }
                        
                    }
                }
            }
            break;
        case "products":
            if(check_set_empty(@$sid) && check_set_empty(@$offset,FALSE)){
                $offset = intval($offset);
                $shop_id = intval($sid);
                $array = array();
                foreach (query("SELECT * FROM `products` WHERE !`product_delete` AND `product_shop` = '".$shop_id."' ORDER BY `product_id` LIMIT 12 OFFSET ".$offset) as $product_detail){
                        $images = UPLOADS.query("SELECT * FROM `products_meta` WHERE `product_meta_key` = 'image' AND `product_id` = '".intval($product_detail['product_id'])."' AND `product_meta_active` != 0 LIMIT 1")->fetch()['product_meta_value'];
                        
                        array_push($array,array(
                            "pid" => $product_detail['product_id'] ,
                            "n" => $product_detail['product_name'] ,
                            "p" => $product_detail['product_price'] ,
                            "np" => (intval($product_detail['product_off'])==0) ? $product_detail['product_price'] : $product_detail['product_off'],
                            "l" => $product_detail['product_likes'] ,
                            "t" => $product_detail['product_delivery_time'] ,
                            //"d" => $product_detail['product_description'] ,
                            "e" => $product_detail['product_exist'] ,
                            "i" => $images
                        ));
                }
                echo json_encode($array,JSON_UNESCAPED_UNICODE);
            }
            break;
        case 'updateProduct':
            if(check_set_empty(@$username) && check_set_empty(@$password) && check_set_empty(@$pid)){
                $result = query("SELECT * FROM `users` WHERE `user_login` = '".username($username)."' AND `user_pass` = '".password($password)."'");
                if ( $result->rowCount() == 1){
                    $fetch = $result->fetch();
                    $user_approve = intval($fetch['user_approve']);
                    if($user_approve == 1){
                        $shop = query("SELECT `shop_id` FROM `shops` WHERE `shop_admin` = ".$fetch['users_ID']);
                        if($shop->rowCount() == 1){
                            $shop = $shop->fetch()['shop_id'];
                            $pid = intval($pid);
                            query("UPDATE `products` SET `product_exist`=IF(`product_exist`=1,0,1) WHERE `product_id` = '".$pid."' AND `product_shop` = ".$shop);
                            echo response("UPDATED");
                            
                        }else{
                            echo response("NOT_ANY_SHOP_ADMIN");
                        }
                        
                    }else{
                        echo response("USER_LOCK");
                    }

                }else{ // Check user
                    echo response("USER_NOT_FOUND");
                }
            }
            break;
    }
}
?>