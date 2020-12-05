<?php
include 'includes/header.php';
permission("users",true);
$type = isset($_GET['type']) ? $_GET['type'] : "all";
if(isset($action)){
    if($action == "block" && isset($userId) && !empty($userId)){
        user_lock($userId);
    }else if($action == "unBlock" && isset($userId) && !empty($userId)){
        user_lock($userId,false);
    }else if($action == "addUser" && isset($username) && !empty($username)&& isset($password) && !empty($password)&& isset($displayName) && !empty($displayName)&& isset($phone) && !empty($phone)){
        $add = addUser($username,$password,$displayName,$phone);
        if($add){
            ok("با موفقیت افزوده شد! شماره کاربری: ".$db->lastInsertId());
            $user_id = $db->lastInsertId();
            if(isset($card) && !empty($card)){
                user_update("card",$user_id,$card);
            }
            if(isset($position) && !empty($position)){
                user_update("position",$user_id,$position);
            }
        }
    }
}
if(isset($type)){
    $PHP_SELF = $PHP_SELF."?type=".$type;
    if($type=="all"){
        @$s = cleanInput($_GET['s']);
        $sql = (isset($_GET['s'])) ? "SELECT * FROM `users` WHERE `user_login` LIKE '%$s%' OR `user_display_name` LIKE '%$s%' OR `user_phone` LIKE '%$s%'" :"SELECT * FROM `users` LIMIT 50";
        echo ' 
        <header class="top"> <i class="fa fa-users" aria-hidden="true"></i> کاربران <form action="'.$PHP_SELF.'"><input name="s" placeholder="جستجو"><input type="submit" hidden> </form></header>
        <table class="tbl">
        <tr class="top">
            <td>#</td>
            <td>نام</td>
            <td>نام کاربری</td>
            <td>نمایه</td>
            <td>وضعیت</td>
        </tr>
        ';
        // ✔ ✖
        $result = query($sql);
        $i = 0;
        foreach ($result as $pr){
            $i++;
            $info = query("SELECT * FROM `users_meta` WHERE `users_meta_user_id` = ".$pr['users_ID']);
            foreach ($info as $print=>$key){
                $$key['users_meta_key'] = cleanInput($key['users_meta_value']);
            }
            $avatar = AVATAR.@$avatar;
            $avatar = ($avatar==AVATAR) ? $avatar."avatar_square_blue.png" : $avatar ;
            $block = ($pr['user_approve']==1) ? "<b><a style='color: rgb(218, 0, 0) !important' href='users.php?action=block&userId=".$pr['users_ID']."'>✖</a></b>" : "<b><a style='color: green !important' href='users.php?action=unBlock&userId=".$pr['users_ID']."'>✔</a></b>";
            echo '<tr>
                    <td>'.$i.'</td>
                    <td><a href="users.php?type=show&userID='.$pr['users_ID'].'">'.$pr["user_display_name"].'</a></td>
                    <td>'.$pr["user_login"].'</td>
                    <td><img class="avatar" src="'.$avatar.'"></td>
                    <td>'.$block.'</td>
                </tr>
            ';
            unset($avatar);
        }
        echo "</table>
                <!-- <a href='users.php?type=add'><div class='btn btn-green'><i class='fa fa-user-plus'></i>  افزودن کاربر</div> </a> -->
            ";
    }else if($type=="block"){
        @$s = cleanInput($_GET['s']);
        $sql = (isset($_GET['s'])) ? "SELECT * FROM `users` WHERE `user_login` LIKE '%$s%' OR `user_display_name` LIKE '%$s%' OR `user_phone` LIKE '%$s%' AND `user_approve` = 0" :"SELECT * FROM `users`  WHERE `user_approve` = 0 LIMIT 50 ";
        echo ' 
        <header class="top"> <i class="fa fa-users" aria-hidden="true"></i> کاربران <form action="'.$PHP_SELF.'"><input name="s" placeholder="جستجو"><input name="type" value="block" hidden><input type="submit" hidden> </form></header>
        <table class="tbl">
        <tr class="top">
            <td>#</td>
            <td>نام</td>
            <td>نام کاربری</td>
            <td>نمایه</td>
            <td>وضعیت</td>
        </tr>
        ';
        // ✔ ✖
        $result = query($sql);
        $i = 0;
        foreach ($result as $pr){
            $i++;
            $info = query("SELECT * FROM `users_meta` WHERE `users_meta_user_id` = ".$pr['users_ID']);
            foreach ($info as $print=>$key){
                $$key['users_meta_key'] = cleanInput($key['users_meta_value']);
            }
            $avatar = AVATAR.$avatar;
            $block = ($pr['user_approve']==1) ? "<b><a style='color: rgb(218, 0, 0) !important' href='users.php?action=block&userId=".$pr['users_ID']."'>✖</a></b>" : "<b><a style='color: green !important' href='users.php?type=block&action=unBlock&userId=".$pr['users_ID']."'>✔</a></b>";
            echo '<tr>
                    <td>'.$i.'</td>
                    <td><a href="users.php?type=show&userID='.$pr['users_ID'].'">'.$pr["user_display_name"].'</a></td>
                    <td>'.$pr["user_login"].'</td>
                    <td><img class="avatar" src="'.$avatar.'"></td>
                    <td>'.$block.'</td>
                </tr>
            ';
        }
        echo "</table>
            ";
    }else if($type=="show" && isset($userID) && !empty($userID)) {

    }else if($type=="add"){
        if(isset($addUser)){
            ok('با موفقیت افزوده شد! آیدی کاربری: '.$db->lastInsertId());
        }else if(isset($notAddUser)){
            error("افزوده نشد!");
        }
        echo '<header class="top"><i class="fa fa-user-plus" aria-hidden="true"></i> افزودن کاربر</header>
              <div class="form">
              <form action="users.php?type=add&action=addUser" method="post">
                <label>نام کاربری: <input type="text" placeholder="نام کاربری" name="username"></label>
                <label>کلمه عبور: <input type="text" placeholder="کلمه عبور" name="password"></label>
                <label>نام نمایشی: <input type="text" placeholder="نام نمایشی" name="displayName"></label><br><br>
                <label>تلفن همراه: <input type="text" placeholder="تلفن همراه" name="phone"></label>
                <label>شماره کارت اعتباری(اختیاری): <input type="text" placeholder="شماره کارت" name="card"></label><br><br>
                <label>جایگاه نمایشی(اختیاری): <input type="text" placeholder="جایگاه نمایشی" name="position"></label>
               <input type="submit" value="تایید" class=\'btn btn-green float-left\'><br><br>
              </form>
            </div>
        ';
    }
}
include 'includes/footer.php';
?>