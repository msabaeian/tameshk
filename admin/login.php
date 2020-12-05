<?php
session_start();

$_SESSION['user_id_admin_panel'] = 1;
die();
if(isset($_SESSION['admin'])){
    header("Location: index.php");
}else{

}
include '../config.inc.php';
include '../functions.php';
include '../module/php/jdf.php';
@$action = $_GET['action'];
if($action == 'out'){
    session_destroy();
    header("location: login.php");
}else{

    if(isset($_POST['log']))
    {
        
        if(isset($_SESSION['captcha'])){
            echo num(mb_strtolower($_POST['captcha']));
            if($_SESSION['captcha']==num(mb_strtolower($_POST['captcha'])))
            {
                // Start
                if(!empty($_POST['username']) && !empty($_POST['password']))
                {
                   
                    $sql = "SELECT * FROM `users` WHERE `user_login` = ? AND `user_pass` = ?";
                    $result = $db->prepare($sql);
                    $result->bindValue(1,username($_POST['username']));
                    $result->bindValue(2,Mhasher($_POST['password']));
                    $result->execute();
                    if($result->rowCount()==1)
                    {
                        $fetch = $result->fetch();
                        if(intval($fetch['user_status'])==2 || intval($fetch['user_status'])==3){
                            if(intval($fetch['user_approve'])==1){
                                $_SESSION['user_id_admin_panel'] = intval($fetch['users_ID']);
                               echo ' <script> location.replace("index.php"); </script>';
                            }else{
                                echo "Your account is Lock!";
                            }

                            /*$sql = "INSERT INTO `user_login_log`(`user_id`, `login_device_key`, `user_status_now`, `login_date`) VALUES (?,?,?,?)";
                            $insert = $db->prepare($sql);
                            $insert->bindValue(1,$fetch['id']);
                            $insert->bindValue(2,$_SERVER['HTTP_USER_AGENT']." | ".get_ip());
                            $insert->bindValue(3,$fetch['user_status']);
                            $insert->bindValue(4,date("Y-m-d H:i:s",time()));
                            if($insert->execute()){

                            }*/

                        }else if($fetch['user_status']=="Liable"){
                            if($fetch['lock']=="no"){
                                $_SESSION['liable'] = intval($fetch['id']);
                                $sql = "INSERT INTO `user_login_log`(`user_id`, `login_device_key`, `user_status_now`, `login_date`) VALUES (?,?,?,?)";
                                $insert = $db->prepare($sql);
                                $insert->bindValue(1,$fetch['id']);
                                $insert->bindValue(2,$_SERVER['HTTP_USER_AGENT']." | ".get_ip());
                                $insert->bindValue(3,$fetch['user_status']);
                                $insert->bindValue(4,date("Y-m-d H:i:s",time()));
                                if($insert->execute()){
                                    header("Location:".ADDR."liable");
                                }
                            }else{
                                echo 'حساب کاربری شما مسدود شده است!';
                            }

                        }else{

                        }

                    }else
                    {
                        echo 'Wrong username or password';
                    }
                }else
                {
                    echo 'Please insert your username and password';
                }
                //End
            }
        }else{
            echo 'System Error!';
        }
    }
    $al = array_merge(range('A','Z') , range(0,9));
    $cap = "";
    for($i=0;$i<=4;$i++)
        $cap .= $al[rand(0,35)];
    $_SESSION['captcha'] = num(1);//num(mb_strtolower($cap));

}
$images = array();
foreach(glob('../images/stockBG/*.*') as $filename){
    array_push($images,$filename);
}
$images = $images[rand(0,sizeof($images)-1)];
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Login</title>
    <style>
        @import "../module/fonts/IranSans/css/fontiran.css";
        *{padding:0; margin:0;}
        body{background:url(<?php echo $images; ?>) no-repeat ; background-size: cover; background-position: center center; color: #fff;direction: rtl; font-family: IRANSans}
        .box{background:rgba(200,200,200,.8); padding:20px; width:350px; box-shadow:0 0 5px #767676; border-radius:5px; margin-top: 7%}
        #input{padding:10px; width: 200px; border:none;}
        img{border-radius:5px;}
        .log{cursor:pointer; background: rgb(26,179,149); padding: 5px; width: 180px;}
    </style>
</head>

<body>
<center>
    <div class="box">
        <form action="" method="post">
            <h2 style="text-shadow: 0 0 2px #000;">ورود به پنل مدیریت</h2><br>
            <input name="username" id="input" placeholder="نام کاربری" type="text" autofocus><br><br>
            <input name="password" id="input" placeholder="کلمه عبور" type="password"><br><br>
            <img src="../module/php/captcha.php"><br><br>
            <input id="input" name="captcha" style="text-align: center;" maxlength="5" placeholder="کد امنیتی" type="text"><br><br>
            <input id="input" style="color: #fff;" class="log" name="log" type="submit" value="ورود">
        </form>
    </div>
</center>
</body>
</html>