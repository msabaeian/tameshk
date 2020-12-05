<?php
include 'autoload.php';
include 'admin_info.php';
$PHP_SELF = basename($_SERVER['PHP_SELF']);

foreach ($_POST as $pr=>$key){
    IF(!is_array($key)){
        $$pr = cleanInput($key);
    }else{
        $$pr = $key;
    }
    
}
foreach ($_GET as $pr=>$key){
    IF(!is_array($key)){
        $$pr = cleanInput($key);
    }else{
        $$pr = $key;
    }
}
global $page_names;
?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <title>پنل مدیریت</title>
    <link href="../module/css/admin_style.css" rel="stylesheet" type="text/css">
    <link href="../module/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <script src="../module/js/jquery-1.11.0.min.js"></script>
    <script src="../module/js/jquery.slimscroll.min.js"></script>
    <script src="../module/js/pace.min.js"></script>
    <script src="../module/js/notify.min.js"></script>

</head>
<body>
<aside class="aside">

    <img src="<?php echo AVATAR.$avatar; ?>" alt="<?php echo $admin_display_name; ?>" width="80" height="80">
    <br><h4 style="display: inline;float: right;margin-right: 10px;"><?php echo $admin_display_name; ?></h4>
    <br><h5 style="display: inline;float: right;margin-right: 10px;color: rgb(48, 186, 160);"><?php echo $user_position_name; ?></h5>
    <br><a href="login.php?action=out" class="logout"><li><i class="fa fa-close"></i></a>
    <br><br>

<?php

    if(permission("orders")) {
        echo '
        
        <ul class="aside-ul">
            <a href="order.php"> <li  data-page-name="order.php?type=all"><i class="fa fa-first-order" aria-hidden="true"></i> ' . $page_names['order'] . '</li></a>
            <ul>
                <a href="order.php?type=all"> <li data-page-name="order.php?type=all"><i class="fa fa-first-order" aria-hidden="true"></i> ' . $page_names['all-order'] . '</li></a>
                <a href="order.php?type=done"> <li data-page-name="order.php?type=done"><i class="fa fa-sticky-note" aria-hidden="true"></i> ' . $page_names['done-order'] . '</li></a>
                <a href="order.php?type=undone"> <li data-page-name="order.php?type=undone"><i class="fa fa-sticky-note" aria-hidden="true"></i> ' . $page_names['undone-order'] . '</li></a>
            </ul>
        </ul>';
    }

    if(permission("payments")) {
        echo '
        
        <ul class="aside-ul">
            <a href="payments.php"> <li  data-page-name="payments.php?type=all"><i class="fa fa-dollar" aria-hidden="true"></i> ' . $page_names['payments'] . '</li></a>
            <ul>
                <a href="payments.php?type=all"> <li data-page-name="payments.php?type=all"><i class="fa fa-dollar" aria-hidden="true"></i> ' . $page_names['all-payments'] . '</li></a>
                <a href="payments.php?type=done"> <li data-page-name="payments.php?type=done"><i class="fa fa-dollar" aria-hidden="true"></i> ' . $page_names['done-payments'] . '</li></a>
                <a href="payments.php?type=undone"> <li data-page-name="payments.php?type=undone"><i class="fa fa-dollar" aria-hidden="true"></i> ' . $page_names['undone-payments'] . '</li></a>
            </ul>
        </ul>';
    }

    if(permission("dashboard")) {
    echo '
    
    <ul class="aside-ul">
        <a href="#"> <li><i class="fa fa-windows" aria-hidden="true"></i> ' . $page_names['dashboard'] . '</li></a>
        <ul>
            <a href="#"> <li><i class="fa fa-sticky-note" aria-hidden="true"></i> ' . $page_names['notes'] . '</li></a>
        </ul>
    </ul>';
    }

    if(permission("cities")){
    echo '
    <ul class="aside-ul">
        <a href="city.php"> <li data-page-name="city.php?type=all"><i class="fa fa-map" aria-hidden="true"></i> '.$page_names['cities'].'</li></a>
        <ul>
            <a href="city.php?type=all"> <li data-page-name="city.php?type=all"><i class="fa fa-map-signs" aria-hidden="true"></i> '.$page_names['online-cities'].'</li></a>
            <a href="city.php?type=add"> <li data-page-name="city.php?type=add"><i class="fa fa-map-marker" aria-hidden="true"></i> '.$page_names['add-city'].'</li></a>
        </ul>
    </ul>';
    }

    if(permission("shopy")) {
        echo '
    <ul class="aside-ul">
        <a href="shopy.php"> <li data-page-name="shopy.php"><i class="fa fa-shopping-bag" aria-hidden="true"></i> '.$page_names['shopy'].'</li></a>
        <ul>
            <a href="shopy.php?type=sections"> <li data-page-name="shopy.php?type=sections"><i class="fa fa-list-ul" aria-hidden="true"></i> '.$page_names['sections'].'</li></a>
            <a href="shopy.php?type=shops"> <li data-page-name="shopy.php?type=shops"><i class="fa fa-shopping-cart" aria-hidden="true"></i> '.$page_names['shops'].'</li></a>
            <a href="shopy.php?type=addShop"> <li data-page-name="shopy.php?type=addShop"><i class="fa fa-plus" aria-hidden="true"></i> '.$page_names['add-shop'].'</li></a>
            <ul class="aside-ul sub-ul">
                <ul>
                <a href="shopy.php?type=products"> <li data-page-name="shopy.php?type=products"><i class="fa fa-list-alt" aria-hidden="true"></i> '.$page_names['products'].'</li></a>
                <a href="shopy.php?type=addProduct"> <li data-page-name="shopy.php?type=addProduct"><i class="fa fa-plus-square" aria-hidden="true"></i> '.$page_names['add-product'].'</li></a>
                <a href="shopy.php?type=hotProducts"> <li data-page-name="shopy.php?type=hotProducts"><i class="fa fa-star" aria-hidden="true"></i> '.$page_names['hot-products'].'</li></a>
                </ul>
            </ul>
        </ul>
    </ul>';
    }

    if(permission("discount")) {
        echo '
        <ul class="aside-ul">
            <a href="#"> <li data-page-name="discount.php?type=all"><i class="fa fa-dollar" aria-hidden="true"></i> ' . $page_names['discount'] . '</li></a>
            <ul>
                <a href="discount.php?type=all"> <li data-page-name="discount.php?type=all"><i class="fa fa-comment" aria-hidden="true"></i> ' . $page_names['all-discount'] . '</li></a>
                <a href="discount.php?type=add"> <li data-page-name="discount.php?type=add"><i class="fa fa-close" aria-hidden="true"></i> ' . $page_names['add-discount'] . '</li></a>
            </ul>
        </ul>';
    }

    if(permission("users")){
    echo '
    <ul class="aside-ul">
        <a href="users.php"> <li data-page-name="users.php?type=all"><i class="fa fa-user" aria-hidden="true"></i> '.$page_names['user'].' </li></a>
        <ul>
            <a href="users.php?type=all"> <li data-page-name="users.php?type=all"><i class="fa fa-users" aria-hidden="true"></i> ' .$page_names['users'].'</li></a>
            <a href="users.php?type=block"> <li data-page-name="users.php?type=block"><i class="fa fa-lock" aria-hidden="true"></i> '.$page_names['blocked-users'].'</li></a>
            <a href="users.php?type=add"> <li data-page-name="users.php?type=add"><i class="fa fa-user-plus" aria-hidden="true"></i> '.$page_names['add-user'].'</li></a>
        </ul>
    </ul>
    ';
    }

    if(permission("delivery")){
    echo '
    <!-- <ul class="aside-ul">
        <a href="delivery.php"> <li data-page-name="delivery.php"><i class="fa fa-truck" aria-hidden="true"></i> '.$page_names['delivery'].' </li></a>
        <ul>
            <a href="delivery.php?type=centers"> <li data-page-name="delivery.php?type=centers"><i class="fa fa-train" aria-hidden="true"></i> ' .$page_names['delivery-centers'].'</li></a>
            <a href="delivery.php?type=add-centers"> <li data-page-name="delivery.php?type=add-centers"><i class="fa fa-ambulance" aria-hidden="true"></i> '.$page_names['delivery-add-centers'].'</li></a>
            <a href="delivery.php?type=add-driver"> <li data-page-name="delivery.php?type=add-driver"><i class="fa fa-motorcycle" aria-hidden="true"></i> '.$page_names['delivery-driver'].'</li></a>
            <a href="delivery.php?type=online"> <li data-page-name="delivery.php?type=online"><i class="fa fa-bicycle" aria-hidden="true"></i> '.$page_names['delivery-online'].'</li></a>
        </ul>
    </ul> -->
    ';
    }
    if(permission("comment")) {
        echo '
    <ul class="aside-ul">
        <a href="#"> <li><i class="fa fa-comments" aria-hidden="true"></i> ' . $page_names['comment'] . '</li></a>
        <ul>
            <a href="comments.php?type=new"> <li data-page-name="comments.php?type=new"><i class="fa fa-commenting" aria-hidden="true"></i> ' . $page_names['new-comments'] . '</li></a>
            <a href="comments.php?type=accept"> <li data-page-name="comments.php?type=accept"><i class="fa fa-comment" aria-hidden="true"></i> ' . $page_names['approved-comments'] . '</li></a>
            <a href="comments.php?type=reject"> <li data-page-name="comments.php?type=reject"><i class="fa fa-close" aria-hidden="true"></i> ' . $page_names['rejected-comments'] . '</li></a>
        </ul>
    </ul>';
    }

    if(permission("gallery")) {
        echo '
    <ul class="aside-ul">
        <a href="#"> <li><i class="fa fa-camera" aria-hidden="true"></i> ' . $page_names['gallery'] . '</li></a>
        <ul>
            <a href="gallery.php?type=new"> <li data-page-name="gallery.php?type=new"><i class="fa fa-commenting" aria-hidden="true"></i> ' . $page_names['new-gallery'] . '</li></a>
            <a href="gallery.php?type=accept"> <li data-page-name="gallery.php?type=accept"><i class="fa fa-comment" aria-hidden="true"></i> ' . $page_names['accept-gallery'] . '</li></a>
            <a href="gallery.php?type=reject"> <li data-page-name="gallery.php?type=reject"><i class="fa fa-close" aria-hidden="true"></i> ' . $page_names['reject-gallery'] . '</li></a>
        </ul>
    </ul>';
    }

    ?>
    <br><br>
</aside>
<main>