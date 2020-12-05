<?php
include '../../config.inc.php';
include '../../functions.php';
include  '../includes/admin_functions.php';

if ( is_session_started() === FALSE ) session_start();
isAdmin();
foreach ($_POST as $pr=>$key){
    $$pr = cleanInput($key);
}
foreach ($_GET as $pr=>$key){
    $$pr = cleanInput($key);
}
?>