<?php
include 'includes/header.php';
permission("comment",true);

$type = isset($_GET['type']) ? $_GET['type'] : "all";
if(isset($type)) {
    $PHP_SELF = $PHP_SELF . "?type=" . $type;
    if($type=="new"){ // new comments
        @$s = cleanInput($_GET['s']);
        $sql = (isset($_GET['s'])) ? "SELECT `image_id`,`image_user_id`,`image_cid`,`image_url`,`image_caption`,`user_display_name`,`city_name` FROM `gallery` as gallery LEFT JOIN users AS users on users.users_ID = gallery.image_user_id LEFT JOIN cities as cities on cities.city_id = gallery.image_cid
WHERE `image_status` = 0 AND `image_caption` LIKE '%$s%' OR user_display_name LIKE '%$s%' LIMIT 30" :"SELECT `image_id`,`image_user_id`,`image_cid`,`image_url`,`image_caption`,`user_display_name`,`city_name` FROM `gallery` as gallery LEFT JOIN users AS users on users.users_ID = gallery.image_user_id LEFT JOIN cities as cities on cities.city_id = gallery.image_cid
WHERE `image_status` = 0 LIMIT 30";
        echo ' 
        <header class="top"> <i class="fa fa-camera" aria-hidden="true"></i> تصاویر جدید <form action="'.$PHP_SELF.'"><input name="s" placeholder="جستجو"><input name="type" value="sections" hidden><input type="submit" hidden> </form></header>
        <table class="tbl">
        <tr class="top">
            <td>#</td>
            <td>کاربر</td>
            <td>شهر</td>
            <td>کپشن</td>
            <td>تصویر</td>
            <td style="width: 150px;">عمل</td>
        </tr>
        ';
        // ✔ ✖
        $result = query($sql);
        foreach ($result as $pr){
            $active = '<span class="color-red pointer rejectGallery" GalleryId="'.$pr['image_id'].'" title="رد تصویر">رد تصویر</span>' .'<br><span class="color-green pointer acceptGallery" GalleryId="'.$pr['image_id'].'" title="فعال سازی">تایید</span>' ;
            echo '<tr>
                    <td>'.$pr['image_id'].'</td>
                    <td><a href="users.php?type=show&userID='.$pr['image_user_id'].'">'.$pr["user_display_name"].'</a></td>
                    <td><a href="city.php?type=show&cid='.$pr['image_cid'].'">'.$pr["city_name"].'</a></td>
                    <td>'.$pr['image_caption'].'</td>
                    <td><a href="'.UPLOADS.$pr['image_url'].'" target="_blank">نمایش</a></td>
                    <td>'.$active.'</td>
                </tr>
            ';
        }
        echo "</table>";
    }else if($type=="accept"){ // accept Gallery
        @$s = cleanInput($_GET['s']);
        $sql = (isset($_GET['s'])) ? "SELECT `image_id`,`image_user_id`,`image_cid`,`image_url`,`image_caption`,`user_display_name`,`city_name` FROM `gallery` as gallery LEFT JOIN users AS users on users.users_ID = gallery.image_user_id LEFT JOIN cities as cities on cities.city_id = gallery.image_cid
WHERE `image_status` = 1 AND `image_caption` LIKE '%$s%' OR user_display_name LIKE '%$s%' LIMIT 30" :"SELECT `image_id`,`image_user_id`,`image_cid`,`image_url`,`image_caption`,`user_display_name`,`city_name` FROM `gallery` as gallery LEFT JOIN users AS users on users.users_ID = gallery.image_user_id LEFT JOIN cities as cities on cities.city_id = gallery.image_cid
WHERE `image_status` = 1 LIMIT 30";
        echo ' 
        <header class="top"> <i class="fa fa-camera" aria-hidden="true"></i> تصاویر جدید <form action="'.$PHP_SELF.'"><input name="s" placeholder="جستجو"><input name="type" value="sections" hidden><input type="submit" hidden> </form></header>
        <table class="tbl">
        <tr class="top">
            <td>#</td>
            <td>کاربر</td>
            <td>شهر</td>
            <td>کپشن</td>
            <td>تصویر</td>
            <td style="width: 150px;">عمل</td>
        </tr>
        ';
        // ✔ ✖
        $result = query($sql);
        foreach ($result as $pr){
            $active = '<span class="color-red pointer rejectGallery" GalleryId="'.$pr['image_id'].'" title="رد تصویر">رد تصویر</span>' .'<br><span class="color-green pointer acceptGallery" GalleryId="'.$pr['image_id'].'" title="فعال سازی">تایید</span>' ;
            echo '<tr>
                    <td>'.$pr['image_id'].'</td>
                    <td><a href="users.php?type=show&userID='.$pr['image_user_id'].'">'.$pr["user_display_name"].'</a></td>
                    <td><a href="city.php?type=show&cid='.$pr['image_cid'].'">'.$pr["city_name"].'</a></td>
                    <td>'.$pr['image_caption'].'</td>
                    <td><a href="'.UPLOADS.$pr['image_url'].'" target="_blank">نمایش</a></td>
                    <td>'.$active.'</td>
                </tr>
            ';
        }
        echo "</table>";
    }else if($type=="reject"){ // reject Gallery
        @$s = cleanInput($_GET['s']);
        $sql = (isset($_GET['s'])) ? "SELECT `image_id`,`image_user_id`,`image_cid`,`image_url`,`image_caption`,`user_display_name`,`city_name` FROM `gallery` as gallery LEFT JOIN users AS users on users.users_ID = gallery.image_user_id LEFT JOIN cities as cities on cities.city_id = gallery.image_cid
    WHERE `image_status` = -1 AND `image_caption` LIKE '%$s%' OR user_display_name LIKE '%$s%' LIMIT 30" :"SELECT `image_id`,`image_user_id`,`image_cid`,`image_url`,`image_caption`,`user_display_name`,`city_name` FROM `gallery` as gallery LEFT JOIN users AS users on users.users_ID = gallery.image_user_id LEFT JOIN cities as cities on cities.city_id = gallery.image_cid
    WHERE `image_status` = -1 LIMIT 30";
        echo ' 
        <header class="top"> <i class="fa fa-camera" aria-hidden="true"></i> تصاویر جدید <form action="'.$PHP_SELF.'"><input name="s" placeholder="جستجو"><input name="type" value="sections" hidden><input type="submit" hidden> </form></header>
        <table class="tbl">
        <tr class="top">
            <td>#</td>
            <td>کاربر</td>
            <td>شهر</td>
            <td>کپشن</td>
            <td>تصویر</td>
            <td style="width: 150px;">عمل</td>
        </tr>
        ';
        // ✔ ✖
        $result = query($sql);
        foreach ($result as $pr){
            $active = '<span class="color-red pointer rejectGallery" GalleryId="'.$pr['image_id'].'" title="رد تصویر">رد تصویر</span>' .'<br><span class="color-green pointer acceptGallery" GalleryId="'.$pr['image_id'].'" title="فعال سازی">تایید</span>' ;
            echo '<tr>
                    <td>'.$pr['image_id'].'</td>
                    <td><a href="users.php?type=show&userID='.$pr['image_user_id'].'">'.$pr["user_display_name"].'</a></td>
                    <td><a href="city.php?type=show&cid='.$pr['image_cid'].'">'.$pr["city_name"].'</a></td>
                    <td>'.$pr['image_caption'].'</td>
                    <td><a href="'.UPLOADS.$pr['image_url'].'" target="_blank">نمایش</a></td>
                    <td>'.$active.'</td>
                </tr>
            ';
        }
        echo "</table>";
    }
}
include 'includes/footer.php'; ?>