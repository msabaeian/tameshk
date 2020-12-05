<?php

/* Clean any input and any bugs in them value */
function cleanInput($input) {

    $search = array(
        '@<script[^>]*?>.*?</script>@si',   // Strip out javascript
        '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
        '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
        '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
    );

    $output = preg_replace($search, '', $input);
    $output = htmlspecialchars($output, ENT_QUOTES);
    return $output;
}

/* Get real ip */
function get_ip()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    return $ip ;

}

function phone($phone){
        return cleanInput(preg_replace('/\s+/', '', $phone));
}
/* change numbers style to persian or english and add dot(,) beetween 3 num  */
function num($value, $format = false)
{
    $farsi_nums = array('۰','۱','۲','۳','۴','۵','۶','۷','۸','۹');
    $en_nums    = range(0,9);

    if($format)$value = number_format($value, 0, ',',',');
    // ،

    return str_replace($en_nums, $farsi_nums, $value);
}

// Mhasher - a powerfull hasher and NO BODY can decode it!!!
function Mhasher($value){
    $hash = md5(sha1(md5($value)));
    $end = md5(substr($hash,-10,10));
    $str = sha1(substr($hash,-7,5));
    $final = md5($str.$hash.$end);
    return "$/Mhasher/".$final;
}


function curPageURL() {

    $pageURL = 'http';

    if (@$_SERVER["HTTPS"] == "on") {$pageURL .= "s";}

    $pageURL .= "://";

    if ($_SERVER["SERVER_PORT"] != "80") {

        $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];

    } else {

        $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];

    }

    return $pageURL;

}

function is_user(){
    if ( is_session_started() === FALSE ) session_start();
    if(isset($_SESSION['login'])){
        return true;
    }else{
        return false;
    }
}


function isLiable($t = 'redirect'){
    if ( is_session_started() === FALSE ) session_start();
    if(isset($_SESSION['liable'])){
      return true;
    }else{
        $a = ($t=='redirect') ? header("Location:".ADDR."liable/login.php") :  header("location:".ADDR.$t);
    }
}

function is_session_started()
{
    if ( php_sapi_name() !== 'cli' ) {
        if ( version_compare(phpversion(), '5.4.0', '>=') ) {
            return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
        } else {
            return session_id() === '' ? FALSE : TRUE;
        }
    }
    return FALSE;
}

function ok($message){
    echo '<p class="ok">'.$message.'</p>';
}
function error($message){
    echo '<p class="error">'.$message.'</p>';
}

function tweetAddr($id,$date){
    return ADDR."tweet/".mb_substr($date,0,4,'utf-8')."/".$id;
}

function username($username){
    return mb_strtolower(cleanInput(preg_replace('/\s+/', '', $username)));
}

function password($password){
    return Mhasher(mb_strtolower(cleanInput(preg_replace('/\s+/', '', $password))));
}

$message = array(
    "acceptTweet" => "مشکل شما تایید شد و هم اکنون نمایش داده می شود.",
    "rejectTweet" => "مشکل شما رد شد و در سیتسم نمایش داده نخواهد شد.",
    "acceptComment" => "دیدگاه شما تایید شد و هم اکنون در سایت نمایش داده می شود.",
    "rejectComment" => "دیدگاه شما رد شد و در سیتسم نمایش داده نخواهد شد.",
    "done" => "مشکل ارسال شده توسط شما برطرف شد. با تشکر از اطلاع رسانی شما"
);

function queryInsert($table, $data = array()){
    $field='';
    $column='';
    if (is_array($data)) {
        foreach ($data as $k => $v) {
            $field .= '`' . $k . '`, ';
            $column .= '\'' . escape($v) . '\', ';
        }
        $field = substr($field, 0, -2);
        $column = substr($column, 0, -2);
        $ret = "INSERT INTO `$table` ($field) VALUES ($column);";
    } else {
        //$this->error($this->errors['queryinsert']);
        $ret = false;
    }

    return $ret;
}

function escape($string){
    if (get_magic_quotes_gpc()) {
        $string = stripslashes($string);
    }
    $ret = is_numeric($string) ? $string : @cleanInput($string);

    return $ret;

}

function queryUpdate($table, $data = array(), $other = null){
    $sql='';
    if (is_array($data)) {
        foreach ($data as $k => $v) {
            $v = '\''. escape($v) .'\'';
            $sql .= '`' . $k . '`' . ' = ' . $v . ', ';
        }
        $sql = substr($sql, 0, -2);
        $other = is_null($other) ? null : ' ' . $other;
        $ret = "UPDATE `$table` SET $sql$other;";
    } else {
        $ret = false;
    }
    return $ret;
}

function get_date($j = false){
    if ($j) {
        require_once ('./module/php/jdf.php');
        return jdate("Y-m-d H:i:s",time());
    }
    return date("Y-m-d H:i:s",time());
}

function check_set_empty($var , $empty = true){
    return ($empty) ? isset($var) && !empty($var) : isset($var);
}

function query($sql){
    global $db;
    return $db->query($sql);
}

function ago_time($time_ago){
    $time_ago = strtotime($time_ago);
    $cur_time     = time();
    $time_elapsed     = $cur_time - $time_ago;
    $seconds     = $time_elapsed ;
    $minutes     = round($time_elapsed / 60 );
    $hours         = round($time_elapsed / 3600);
    $days         = round($time_elapsed / 86400 );
    $weeks         = round($time_elapsed / 604800);
    $months     = round($time_elapsed / 2600640 );
    $years         = round($time_elapsed / 31207680 );
// Seconds
    if($seconds <= 60){
        return "$seconds ثانیه قبل";
    }
//Minutes
    else if($minutes <=60){
        if($minutes==1){
            return "یک ماه پیش";
        }else{
            return "$minutes دقیقه قبل";
        }
    }
//Hours
    else if($hours <=24){
        if($hours==1){
            return "یک ساعت قبل";
        }else{
            return "$hours ساعت قبل";
        }
    }
//Days
    else if($days <= 7){
        if($days==1){
            return "دیروز";
        }else{
            return "$days روز قبل";
        }
    }
//Weeks
    else if($weeks <= 4.3){
        if($weeks==1){
            return "یک هفته پیش";
        }else{
            return "$weeks هفته پیش";
        }
    }
//Months
    else if($months <=12){
        if($months==1){
            return "یک ماه پیش";
        }else{
            return "$months ماه پیش";
        }
    }
//Years
    else{
        if($years==1){
            return "یک سال پیش";
        }else{
            return "$years سال پیش";
        }
    }
}

function startsWith($haystack, $needle)
{
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}
?>