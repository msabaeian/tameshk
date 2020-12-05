<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 5/30/2017
 * Time: 7:02 PM
 */
require_once 'api.functions.php';

if(check_set_empty(@$type)){
    switch ($type){
        case 'openApp':
            if(check_set_empty(@$username,false) && check_set_empty(@$password,false)){
                if($username == 0 || $username == "0"){
                    echo_json(array(
                            "uid" => "USER_NOT_FOUND",
                            "AndroidLastVersionCode" => AndroidLastVersionCode,
                            "AndroidLastVersionCodeWorks" => AndroidLastVersionCodeWorks 
                    ));
                }else{
                    $result = query("SELECT * FROM `users` WHERE `user_login` = '".username($username)."' AND `user_pass` = '".password($password)."'");
                    if ( $result->rowCount() == 1){
                        $fetch = $result->fetch();
                        $user_approve = intval($fetch['user_approve']);
                        if($user_approve == 1){
                            echo_json(array(
                                "uid" => intval($fetch['users_ID']),
                                "udn" => cleanInput($fetch['user_display_name']),
                                "ub" => intval($fetch['user_balance']) ,
                                "upn" => cleanInput($fetch['user_phone']),
                                "AndroidLastVersionCode" => AndroidLastVersionCode,
                                "AndroidLastVersionCodeWorks" => AndroidLastVersionCodeWorks 
                            ));
                        }else{
                            echo response("USER_LOCK");
                        }

                    }else{ // Check user
                        echo_json(array(
                            "uid" => "USER_NOT_FOUND",
                            "AndroidLastVersionCode" => AndroidLastVersionCode,
                            "AndroidLastVersionCodeWorks" => AndroidLastVersionCodeWorks 
                        ));
                    }
                }
            }
            break;
        case 'login':
            if(check_set_empty(@$username) && check_set_empty(@$password)){
                $result = query("SELECT * FROM `users` WHERE `user_login` = '".username($username)."' AND `user_pass` = '".password($password)."'");
                if ( $result->rowCount() == 1){
                    $fetch = $result->fetch();
                    $user_approve = intval($fetch['user_approve']);
                    if($user_approve == 1){
                        echo_json(array(
                            "uid" => intval($fetch['users_ID']),
                            "udn" => cleanInput($fetch['user_display_name']),
                            "ub" => intval($fetch['user_balance']) ,
                            "upn" => cleanInput($fetch['user_phone'])
                        ));
                    }else{
                        echo response("USER_LOCK");
                    }

                }else{ // Check user
                    echo response("USER_NOT_FOUND");
                }
            }
            break;
        case 'register':
            if(check_set_empty(@$phone)){
                $phone = phone($phone);
                $result = query("SELECT `users_ID` FROM `users` WHERE `user_phone` = '".$phone."'");
                if ( $result->rowCount() >= 1){

                   echo  response("USER_PHONE_EXIST");
                }else{ // can register
                    if(canSendSMS($phone)){
                        $uac = rand(10000,99999);
                        sendSms($phone,$uac);
                        echo response("ACTIVE_CODE_SEND");
                    }else{ // 4min
                        echo response("WAIT_4_MIN");
                    }
                }
            }
            break;
        case 'forget':
            if(check_set_empty(@$phone)){
                $phone = phone($phone);
                $result = query("SELECT `users_ID` FROM `users` WHERE `user_phone` = '".$phone."'");
                if ( $result->rowCount() >= 1){
                    if(canSendSMS($phone)){
                        $uac = rand(10000,99999);
                        sendSms($phone,$uac);
                        echo response("ACTIVE_CODE_SEND");
                    }else{ // 4min
                       echo  response("WAIT_4_MIN");
                    }
                }else{ // USER_NOT_FOUND
                   echo  response("USER_NOT_FOUND");
                }
            }
            break;
        case 'active':
            if(check_set_empty(@$phone) && check_set_empty(@$code)){
                $phone = phone($phone);
                $code = intval($code);
                $result = $db->query("SELECT * FROM `sms_log` WHERE `sms_to` = '$phone' AND `sms_content` = '".$code."' ORDER BY `sms_id` DESC LIMIT 1");
                if ( $result->rowCount() >= 1){
                    $USER = query("SELECT `users_ID` FROM `users` WHERE `user_phone` = '".$phone."'");
                    if($USER->rowCount() >= 1){
                        echo response("USER_PHONE_EXIST");
                    }else{
                        $add['user_login'] = $phone;
                        $add['user_pass'] = password($phone);
                        $add['user_phone'] = $phone;
                        $add['user_registered'] = get_date();
                        query(queryInsert('users',$add));
                        echo response("REGISTER_COMPLETE");
                    }

                }else{ // WRONG
                    echo response("WRONG_PHONE_OR_CODE");
                }
            }
            break;
        case 'forget_active':
            if(check_set_empty(@$phone) && check_set_empty(@$code)){
                $phone = phone($phone);
                $code = intval($code);
                $result = $db->query("SELECT * FROM `sms_log` WHERE `sms_to` = '$phone' AND `sms_content` = '".$code."' ORDER BY `sms_id` DESC LIMIT 1");
                if ( $result->rowCount() >= 1){
                    $USER = query("SELECT `users_ID` as uid,`user_phone` AS upn FROM `users` WHERE `user_phone` = '".$phone."'");
                    if($USER->rowCount() >= 1){
                        $USER = $USER->fetch();
                        $update['user_pass'] = password($USER['upn']);
                        $uid = $USER['uid'];
                        query(queryUpdate('users',$update," WHERE `users_ID` = ".$uid));
                        echo response("UPDATED");
                    }else{
                        echo response("USER_NOT_FOUND");
                    }

                }else{ // WRONG
                   echo  response("WRONG_PHONE_OR_CODE");
                }
            }
            break;
        case 'change_password':
            if(check_set_empty(@$username) && check_set_empty(@$password) && check_set_empty(@$new_password)){
                $new_password = preg_replace('/\s+/', '', $new_password);
                $sql = "SELECT `users_ID` as uid FROM `users` WHERE `user_login` = ? AND `user_pass` = ?";
                $result = $db->prepare($sql);
                $result->bindValue(1,username($username));
                $result->bindValue(2,password($password));
                $result->execute();
                if($result->rowCount() >= 1 ){
                    $update['user_pass'] = password($new_password);
                    $uid = $result->fetch()['uid'];
                    query(queryUpdate('users',$update," WHERE `users_ID` = ".$uid));
                    echo response("UPDATED");
                }else{
                    echo response("USER_NOT_FOUND");
                }
            }
            break;
        case 'change_udn':
            if(check_set_empty(@$username) && check_set_empty(@$password) && check_set_empty(@$udn)){
                $sql = "SELECT `users_ID` as uid FROM `users` WHERE `user_login` = ? AND `user_pass` = ?";
                $result = $db->prepare($sql);
                $result->bindValue(1,username($username));
                $result->bindValue(2,password($password));
                $result->execute();
                if($result->rowCount() >= 1 ){
                    $update['user_display_name'] = cleanInput($udn);
                    $uid = $result->fetch()['uid'];
                    query(queryUpdate('users',$update," WHERE `users_ID` = ".$uid));
                    echo response("UPDATED");
                }else{
                    echo response("USER_NOT_FOUND");
                }
            }
            break;
    }
}
?>