<?php
include 'includes/header.php';
permission("delivery",true);

$type = isset($_GET['type']) ? $_GET['type'] : "all";
if(isset($action)){

}
if(isset($type)){
    $PHP_SELF = $PHP_SELF."?type=".$type;
    if($type=="sections") { // Show Sections

    }
}
include 'includes/footer.php';
?>
