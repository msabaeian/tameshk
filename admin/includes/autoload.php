<?php
include '../config.inc.php';
include '../functions.php';
include  'admin_functions.php';
if ( is_session_started() === FALSE ) session_start();
isAdmin();

?>