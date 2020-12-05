<?php

// Call .php?Action=topic&topic=all&data={}
// Call .php?Action=single&token=hfdhdhdhdfhdf&data={}
require_once 'api.functions.php';
$action=$_GET["Action"];

if(check_set_empty(@$type)) {
    switch ($type) {
        case "support":
            if(check_set_empty(@$username) && check_set_empty(@$password) && check_set_empty(@$Token) && check_set_empty(@$Data)  ) {
                
                $result = query("SELECT * FROM `users` WHERE `user_login` = '".username($username)."' AND `user_pass` = '".password($password)."'");
                    if ( $result->rowCount() == 1){
                        $fetch = $result->fetch();
                        $user_approve = intval($fetch['user_approve']);
                        if($user_approve == 1){
                            $token_check = query("SELECT `users_meta_value` AS fcm_token FROM `users_meta` WHERE `users_meta_key` = 'fcm_token' AND `users_meta_user_id` = ".$fetch['users_ID']);
                            if($token_check->rowCount() == 1){
                                $token_check = $token_check->fetch();
                                if($token_check['fcm_token'] != cleanInput($Token)){
                                    query('DELETE FROM `users_meta` WHERE `users_meta_key` = "fcm_token" AND `users_meta_user_id` = '.$fetch["users_ID"]);
                                    $add_token['users_meta_user_id'] = $fetch['users_ID'];
                                    $add_token['users_meta_key'] = 'fcm_token';
                                    $add_token['users_meta_value'] = cleanInput($Token);
                                    query(queryInsert('users_meta',$add_token));
                                }
                            }else{
                                $add_token['users_meta_user_id'] = $fetch['users_ID'];
                                $add_token['users_meta_key'] = 'fcm_token';
                                $add_token['users_meta_value'] = cleanInput($Token);
                                query(queryInsert('users_meta',$add_token));
                            }
                            $support['support_from_uid'] = $fetch['users_ID'];
                            $support['support_message'] = cleanInput($Data);
                            $support['support_to_uid'] = 4;
                            query(queryInsert('support',$support));
                            //$j=json_decode(SendMessageToSingleDevice($t, $d));
                            echo response("MESSAGE_SEND");
                        }else{
                            echo response("MESSAGE_NOT_SEND");
                        }
                    }else{
                       echo response("MESSAGE_NOT_SEND"); 
                    }
            }
            break;

            case "single":            
             if(check_set_empty(@$username) && check_set_empty(@$password) && check_set_empty(@$toUid) && check_set_empty(@$Message)  ) {
                $toUid = intval($toUid); $Message = cleanInput($Message);
                $token_check = query("SELECT `users_meta_value` AS fcm_token FROM `users_meta` WHERE `users_meta_key` = 'fcm_token' AND `users_meta_user_id` = ".$toUid);
                if($token_check->rowCount() == 1){
                    $token_check = $token_check->fetch()['fcm_token'];
                    $result = query("SELECT * FROM `users` WHERE `user_login` = '".username($username)."' AND `user_pass` = '".password($password)."' AND `user_status` = 4");
                    if ( $result->rowCount() == 1){
                        $fetch = $result->fetch();
                        $user_approve = intval($fetch['user_approve']);
                        if($user_approve == 1){
                            $support['support_from_uid'] = $fetch['users_ID'];
                            $support['support_message'] = $Message;
                            $support['support_to_uid'] = $toUid;
                            query(queryInsert('support',$support));
                            json_decode(SendMessageToSingleDevice($token_check, $Message));
                            echo response("MESSAGE_SEND");
                        }else{
                            echo response("USER_LOCK");
                        }
                    }else{
                       echo response("USER_LOCK"); 
                    }
                }else{
                    echo response("USER_NOT_FOUND"); 
                }
                
            } 
            break;
    }
}

function SendMessageToSingleDevice ($Token, $Message)
    {
    // API access key from Google API's Console
        if (!defined('API_ACCESS_KEY')) define( 'API_ACCESS_KEY', '[API_ACCESS_KEY_HERE]' );
        // prep the bundle
        $fields = array
        (
			'to' =>$Token,
            'data' => array(
                "mText" => cleanInput($Message),
                "mTime" => "05:23"
            )
        );

        $headers = array
        (
            'Authorization:key=' . API_ACCESS_KEY,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        curl_close( $ch );
        echo $result;
        return $result;
    }
	
?>

