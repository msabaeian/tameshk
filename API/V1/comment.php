<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 6/4/2017
 * Time: 12:17 AM
 */
require_once 'api.functions.php';

if(check_set_empty(@$type) ) {
    switch ($type) {
        case 'addComment':
            if(check_set_empty(@$username) && check_set_empty(@$password) && check_set_empty(@$star) && check_set_empty(@$text)  && (check_set_empty(@$pid) || check_set_empty(@$sid)) ){
                $result = query("SELECT * FROM `users` WHERE `user_login` = '".username($username)."' AND `user_pass` = '".password($password)."'");
                if ( $result->rowCount() == 1){
                    $fetch = $result->fetch();
                    $user_approve = intval($fetch['user_approve']);
                    if($user_approve == 1){
                        if(check_set_empty($pid)){
                            $comment['comment_product_id'] = intval($pid);
                        }else{
                            $comment['comment_shop_id'] = intval($sid);
                        }
                        $star = floatval($star);
                        $comment['comment_author_id'] = intval($fetch['users_ID']);
                        $comment['comment_text'] = cleanInput($text);
                        $comment['comment_star'] = ($star >= 0 && $star <= 5) ? $star : 5;
                        $comment['comment_date'] = get_date();
                        require_once ('../../module/php/jdf.php');
                        $comment['comment_jdate'] = jdate("Y-m-d H:i:s",time());
                        $comment['comment_author_ip'] = get_ip();
                        query(queryInsert('comments',$comment));
                        echo response("COMMENT_SUBMIT");
                    }else{
                        echo response("USER_LOCK");
                    }

                }else{ // Check user
                    echo response("USER_NOT_FOUND");
                }
            }
            break;
        case 'loadProductComment':
            if(check_set_empty($offset,false) && check_set_empty($pid)){
                $offset = intval($offset);
                $pid = intval($pid);
                $sql = "SELECT * FROM `comments` as comments
                        LEFT JOIN `users` as users ON users.users_ID = comments.`comment_author_id`
                        WHERE comments.`comment_product_id` = '".$pid."' AND comments.`comment_approve`
                        ORDER BY comments.`comment_id` LIMIT 15 OFFSET ".$offset;
                $array = array();
                foreach (query($sql) as $pr){
                    array_push($array,array(
                       "u" =>  $pr['user_display_name'],
                       "s" =>  $pr['comment_star'],
                       "t" =>  $pr['comment_text'],
                       "d" => $pr['comment_jdate']
                    ));
                }
                echo_json($array);
            }
            break;
        case 'loadShopComment':
            if(check_set_empty($offset,false) && check_set_empty($sid)){
                $offset = intval($offset);
                $pid = intval($pid);
                $sql = "SELECT * FROM `comments` as comments
                        LEFT JOIN `users` as users ON users.users_ID = comments.`comment_author_id`
                        WHERE comments.`comment_shop_id` = '".$sid."' AND comments.`comment_approve`
                        ORDER BY comments.`comment_id` LIMIT 15 OFFSET ".$offset;
                $array = array();
                foreach (query($sql) as $pr){
                    array_push($array,array(
                       "u" =>  $pr['user_display_name'],
                       "s" =>  $pr['comment_star'],
                       "t" =>  $pr['comment_text'],
                       "d" => $pr['comment_jdate']
                    ));
                }
                echo_json($array);
            }
            break;
        case 'addLikeProduct':
            if(check_set_empty(@$username) && check_set_empty(@$password) && check_set_empty(@$pid)){
                $result = query("SELECT * FROM `users` WHERE `user_login` = '".username($username)."' AND `user_pass` = '".password($password)."'");
                if ( $result->rowCount() == 1){
                    $fetch = $result->fetch();
                    $user_approve = intval($fetch['user_approve']);
                    if($user_approve == 1){
                        $pid = intval($pid);
                        $uid = intval($fetch['users_ID']);
                        $like = query("SELECT `like_id` FROM `likes` WHERE `like_user_id` = '".$uid."' AND `like_product_id` = ".$pid);
                        if($like->rowCount() >= 1){
                            //query("DELETE FROM `likes` WHERE `like_id` = ".$like->fetch()['like_id']);
                            //echo response("LIKE_DELETED");
                        }else{
                            $add['like_user_id'] = $uid;
                            $add['like_product_id'] = $pid;
                            $add['like_shop_id'] = 0;
                            $add['like_date'] = get_date();
                            query(queryInsert('likes',$add));
                            query("UPDATE `products` SET `product_likes`=`product_likes`+1 WHERE `product_id` = ".$pid);
                            echo response("LIKE_ADDED");
                        }
                    }else{
                        echo response("USER_LOCK");
                    }

                }else{ // Check user
                   echo  response("USER_NOT_FOUND");
                }
            }
            break;
    }
}