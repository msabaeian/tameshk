<?php

define("DB_NAME","[DB_NAME_HERE]");
define("DB_USER","[DB_USER_HERE]");
define("DB_PASS","[DB_PASSWORD_HERE]");
define("ADDR","[URL_HERE]");
define("AVATAR",ADDR."uploads/avatars/");
define("UPLOADS",ADDR."uploads/");
define("API_GETWAY","[GETWAY_TOKEN]");
try{
    $db = new PDO("mysql:host=localhost;dbname=".DB_NAME,DB_USER,DB_PASS,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
}catch(PDOException $error){
    echo "Problem";
}
?>