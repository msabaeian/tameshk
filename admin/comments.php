<?php
include 'includes/header.php';
permission("comment",true);

$type = isset($_GET['type']) ? $_GET['type'] : "all";
if(isset($type)) {
    $PHP_SELF = $PHP_SELF . "?type=" . $type;
    if($type=="new"){ // new comments
        //@$s = cleanInput($_GET['s']);
        $sql = (isset($_GET['s'])) ? "SELECT * FROM `sections` WHERE `delete` = 0 AND `section_name` LIKE '%$s%'" :"SELECT * FROM `comments` as comments LEFT JOIN `users` as users ON users.`users_ID` = `comment_author_id` WHERE !`comment_approve` AND !`comment_approve_by`";
        echo ' 
        <header class="top"> <i class="fa fa-commenting" aria-hidden="true"></i> نظرات جدید <form action="'.$PHP_SELF.'"><input name="s" placeholder="جستجو"><input name="type" value="sections" hidden><input type="submit" hidden> </form></header>
        <table class="tbl">
        <tr class="top">
            <td>#</td>
            <td>کاربر</td>
            <td>متن نظر</td>
            <td>بخش</td>
            <td style="width: 150px;">عمل</td>
        </tr>
        ';
        // ✔ ✖
        $result = query($sql);
        $i = 0;
        foreach ($result as $pr){

            $section = isset($pr['comment_shop_id']) && !empty($pr['comment_shop_id']) ? "SELECT `shop_name` AS sec_name,`shop_id` AS sec_id FROM `shops` WHERE `shop_id` = '".$pr['comment_shop_id']."'" : "SELECT `product_name` AS sec_name,`product_id` AS sec_id FROM `products` WHERE `product_id` = '".$pr['comment_product_id']."'" ;
            $sec_name = query($section)->fetch()['sec_name'];
            $sec_id = query($section)->fetch()['sec_id'];
            $section =  isset($pr['comment_shop_id']) && !empty($pr['comment_shop_id']) ? '<a style="color:rgb(0,150,136) !important" href="shopy.php?type=show&shopId='.$sec_id.'">فروشگاه '.$sec_name.'</a>' : '<a style="color:rgb(0,150,136) !important" href="shopy.php?type=show&productId='.$sec_id.'"> محصول '.$sec_name.'</a>';
            $i++;
            $active = '<span class="color-red pointer rejectComment" commentId="'.$pr['comment_id'].'" title="رد نظر">رد نظر</span>' .'<br><span class="color-green pointer acceptComment" commentId="'.$pr['comment_id'].'" title="فعال سازی">تایید</span>' ;
            echo '<tr>
                    <td>'.$i.'</td>
                    <td><a href="users.php?type=show&userID='.$pr['users_ID'].'">'.$pr["user_display_name"].'</a></td>
                    <td>'.$pr['comment_text'].'</td>
                    <td>
                        '.$section.'
                    </td>
                    <td>'.$active.'</td>
                    
                </tr>
            ';
        }
        echo "</table>";
    }else if($type=="accept"){ // accept comments
        //@$s = cleanInput($_GET['s']);
        $sql = (isset($_GET['s'])) ? "SELECT * FROM `sections` WHERE `delete` = 0 AND `section_name` LIKE '%$s%'" :"SELECT * FROM `comments` as comments LEFT JOIN `users` as users ON users.`users_ID` = `comment_author_id` WHERE `comment_approve`";
        echo ' 
        <header class="top"> <i class="fa fa-commenting" aria-hidden="true"></i> نظرات تایید شده <form action="'.$PHP_SELF.'"><input name="s" placeholder="جستجو"><input name="type" value="sections" hidden><input type="submit" hidden> </form></header>
        <table class="tbl">
        <tr class="top">
            <td>#</td>
            <td>کاربر</td>
            <td>متن نظر</td>
            <td>بخش</td>
            <td style="width: 150px;">عمل</td>
        </tr>
        ';
        // ✔ ✖
        $result = query($sql);
        $i = 0;
        foreach ($result as $pr){

            $section = isset($pr['comment_shop_id']) && !empty($pr['comment_shop_id']) ? "SELECT `shop_name` AS sec_name,`shop_id` AS sec_id FROM `shops` WHERE `shop_id` = '".$pr['comment_shop_id']."'" : "SELECT `product_name` AS sec_name,`product_id` AS sec_id FROM `products` WHERE `product_id` = '".$pr['comment_product_id']."'" ;
            $sec_name = query($section)->fetch()['sec_name'];
            $sec_id = query($section)->fetch()['sec_id'];
            $section =  isset($pr['comment_shop_id']) && !empty($pr['comment_shop_id']) ? '<a style="color:rgb(0,150,136) !important" href="shopy.php?type=show&shopId='.$sec_id.'">فروشگاه '.$sec_name.'</a>' : '<a style="color:rgb(0,150,136) !important" href="shopy.php?type=show&productId='.$sec_id.'"> محصول '.$sec_name.'</a>';
            $i++;
            $active = '<span class="color-red pointer rejectComment" commentId="'.$pr['comment_id'].'" title="رد نظر">رد نظر</span>' ;
            echo '<tr>
                    <td>'.$i.'</td>
                    <td><a href="users.php?type=show&userID='.$pr['users_ID'].'">'.$pr["user_display_name"].'</a></td>
                    <td>'.$pr['comment_text'].'</td>
                    <td>
                        '.$section.'
                    </td>
                    <td>'.$active.'</td>
                    
                </tr>
            ';
        }
        echo "</table>";
    }else if($type=="reject"){ // accept comments
        //@$s = cleanInput($_GET['s']);
        $sql = (isset($_GET['s'])) ? "SELECT * FROM `sections` WHERE `delete` = 0 AND `section_name` LIKE '%$s%'" :"SELECT * FROM `comments` as comments LEFT JOIN `users` as users ON users.`users_ID` = `comment_author_id` WHERE !`comment_approve` AND `comment_approve_by`";
        echo ' 
        <header class="top"> <i class="fa fa-commenting" aria-hidden="true"></i> دیدگاه های رد شده <form action="'.$PHP_SELF.'"><input name="s" placeholder="جستجو"><input name="type" value="sections" hidden><input type="submit" hidden> </form></header>
        <table class="tbl">
        <tr class="top">
            <td>#</td>
            <td>کاربر</td>
            <td>متن نظر</td>
            <td>بخش</td>
            <td style="width: 150px;">عمل</td>
        </tr>
        ';
        // ✔ ✖
        $result = query($sql);
        $i = 0;
        foreach ($result as $pr){

            $section = isset($pr['comment_shop_id']) && !empty($pr['comment_shop_id']) ? "SELECT `shop_name` AS sec_name,`shop_id` AS sec_id FROM `shops` WHERE `shop_id` = '".$pr['comment_shop_id']."'" : "SELECT `product_name` AS sec_name,`product_id` AS sec_id FROM `products` WHERE `product_id` = '".$pr['comment_product_id']."'" ;
            $sec_name = query($section)->fetch()['sec_name'];
            $sec_id = query($section)->fetch()['sec_id'];
            $section =  isset($pr['comment_shop_id']) && !empty($pr['comment_shop_id']) ? '<a style="color:rgb(0,150,136) !important" href="shopy.php?type=show&shopId='.$sec_id.'">فروشگاه '.$sec_name.'</a>' : '<a style="color:rgb(0,150,136) !important" href="shopy.php?type=show&productId='.$sec_id.'"> محصول '.$sec_name.'</a>';
            $i++;
            $active = '<span class="color-green pointer acceptComment" commentId="'.$pr['comment_id'].'" title="فعال سازی">تایید</span>' ;
            echo '<tr>
                    <td>'.$i.'</td>
                    <td><a href="users.php?type=show&userID='.$pr['users_ID'].'">'.$pr["user_display_name"].'</a></td>
                    <td>'.$pr['comment_text'].'</td>
                    <td>
                        '.$section.'
                    </td>
                    <td>'.$active.'</td>
                    
                </tr>
            ';
        }
        echo "</table>";
    }
}
include 'includes/footer.php'; ?>