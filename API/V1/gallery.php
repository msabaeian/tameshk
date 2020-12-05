<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 6/22/2017
 * Time: 09:00 AM
 */
require_once 'api.functions.php';

if(check_set_empty(@$type) ) {
    switch ($type) {
        case 'new':
            if(check_set_empty($offset,false) && check_set_empty($cid)){
                    if(check_set_empty(@$username) && check_set_empty(@$password)){
                        $result = query("SELECT * FROM `users` WHERE `user_login` = '".username($username)."' AND `user_pass` = '".password($password)."'");
                        if ( $result->rowCount() == 1) {
                            $result = $result->fetch();
                            $uid = $result['users_ID'];
                            
                        }
                    }
                    
                $offset = intval($offset);
                $cid = intval($cid);
                $sql = "SELECT * FROM `gallery` as gallery
            	LEFT JOIN users as users ON users.users_ID = gallery.image_user_id
                WHERE gallery.image_cid = '".$cid."' AND gallery.image_status ORDER BY `image_id` DESC LIMIT 5 OFFSET ".$offset;
                
                $array = array();
                foreach (query($sql) as $pr){
                    $like = 0;
                    if(check_set_empty($uid)){
                        
                    $fetch = query("SELECT * FROM `likes` WHERE `like_user_id` = '".intval($uid)."' AND `like_gallery_id` = ".$pr['image_id']);
                    if($fetch->rowCount() > 0) $like =1 ;
                    }
                    
                    
                    array_push($array,array(
                        "id" => $pr['image_id'],
                        "u" => $pr['user_display_name'],
                       "i" =>  $pr['image_url'],
                       "c" =>  $pr['image_caption'],
                       "l" =>  $pr['image_likes'],
                       "d" => $pr['image_jdate'] ,
                       "ul" => $like
                    ));
                }
                echo_json($array);
            }
            break;
        case 'best':
            if(check_set_empty($offset,false) && check_set_empty($cid)){
                    if(check_set_empty(@$username) && check_set_empty(@$password)){
                        $result = query("SELECT * FROM `users` WHERE `user_login` = '".username($username)."' AND `user_pass` = '".password($password)."'");
                        if ( $result->rowCount() == 1) {
                            $result = $result->fetch();
                            $uid = $result['users_ID'];
                            
                        }
                    }
                    
                $offset = intval($offset);
                $cid = intval($cid);
                $sql = "SELECT * FROM `gallery` as gallery
                 LEFT JOIN users as users ON users.users_ID = gallery.image_user_id
                 WHERE gallery.image_status AND DATEDIFF(DATE(now()),`image_date`) < 8 AND gallery.image_cid = '".$cid."'  ORDER BY `image_likes` DESC LIMIT 5 OFFSET ".$offset;
                
                $array = array();
                foreach (query($sql) as $pr){
                    $like = 0;
                    if(check_set_empty($uid)){
                        
                    $fetch = query("SELECT * FROM `likes` WHERE `like_user_id` = '".intval($uid)."' AND `like_gallery_id` = ".$pr['image_id']);
                    if($fetch->rowCount() > 0) $like =1 ;
                    }
                    
                    
                    array_push($array,array(
                        "id" => $pr['image_id'],
                        "u" => $pr['user_display_name'],
                       "i" =>  $pr['image_url'],
                       "c" =>  $pr['image_caption'],
                       "l" =>  $pr['image_likes'],
                       "d" => $pr['image_jdate'] ,
                       "ul" => $like
                    ));
                }
                echo_json($array);
            }
            break;
            
        case 'like':
            if(check_set_empty(@$username) && check_set_empty(@$password) && check_set_empty(@$iid)){
                $result = query("SELECT * FROM `users` WHERE `user_login` = '".username($username)."' AND `user_pass` = '".password($password)."'");
                if ( $result->rowCount() == 1){
                    $fetch = $result->fetch();
                    $user_approve = intval($fetch['user_approve']);
                    if($user_approve == 1){
                        $iid = intval($iid);
                        $uid = intval($fetch['users_ID']);
                        $like = query("SELECT `like_id` FROM `likes` WHERE `like_user_id` = '".$uid."' AND `like_gallery_id` = ".$iid);
                        if($like->rowCount() >= 1){
                            query("DELETE FROM `likes` WHERE `like_id` = ".$like->fetch()['like_id']);
                            query("UPDATE `gallery` SET `image_likes`=`image_likes`-1 WHERE `image_id` = ".$iid);
                            echo response("LIKE_DELETED");
                        }else{
                            $add['like_user_id'] = $uid;
                            $add['like_gallery_id'] = $iid;
                            $add['like_date'] = get_date();
                            query(queryInsert('likes',$add));
                            query("UPDATE `gallery` SET `image_likes`=`image_likes`+1 WHERE `image_id` = ".$iid);
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
            
        case 'send':
            if(check_set_empty(@$username) && check_set_empty(@$password) && check_set_empty(@$cid) && check_set_empty(@$caption,false) & !empty($_FILES)){
                 
                $tmp = $_FILES['file']['tmp_name'];
            	$name = $_FILES['file']['name'];
            	$type = $_FILES['file']['type'];
            	$data = microtime()."-";
            	$data = str_replace(" ","",$data);
            	$add = '../../uploads/sendImages/'.$data.$name;
            	$move = move_uploaded_file($tmp,$add);
            	if($move){
            		$result = query("SELECT * FROM `users` WHERE `user_login` = '".username($username)."' AND `user_pass` = '".password($password)."'");
                if ( $result->rowCount() == 1){
                    $fetch = $result->fetch();
                    $user_approve = intval($fetch['user_approve']);
                    if($user_approve == 1){
                        $comment['image_user_id'] = intval($fetch['users_ID']);
                        $comment['image_cid'] = intval($cid);
                        $comment['image_url'] = $add;
                        $comment['image_caption'] = cleanInput($caption);
                        require_once ('../../module/php/jdf.php');
                        $comment['image_jdate'] = jdate("Y-m-d H:i:s",time());
                        query(queryInsert('gallery',$comment));
                        echo response("COMMENT_SUBMIT");
                    }else{
                        echo response("USER_LOCK");
                    }

                }else{ // Check user
                    echo response("USER_NOT_FOUND");
                }
            	}
                
            }
            break;
    }
}