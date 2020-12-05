<?php
include 'autoload.php';
include  '../includes/admin_info.php';
permission("shopy",true);
@$action = $_POST['action'];
if($action=="deleteCity" && isset($cityId) && !empty($cityId)){
    query("DELETE FROM `sections_meta` WHERE `section_meta_key` = 'city' AND `section_meta_value` = ".intval($cityId));
    echo "ok";
}else if($action=="addCity" && isset($cityId) && !empty($cityId)&& isset($sectionId) && !empty($sectionId)){
    $cityId = intval($cityId);
    $sectionId = intval($sectionId);

    $result = query("SELECT * FROM `sections_meta` WHERE `section_meta_section_id` = '$sectionId' AND  `section_meta_key` = 'city' AND `section_meta_value` = '".$cityId."' AND `meta_active`");
    if($result->rowCount() >= 1){
        echo 'repeat';
    }else{
        query("INSERT INTO `sections_meta`(`section_meta_section_id`, `section_meta_key`, `section_meta_value`, `meta_active`)
            VALUES ('$sectionId','city','$cityId','1')");
        $result = query("SELECT * FROM `cities` WHERE `city_id` = '$cityId'");
        $result = $result->fetch();
        if($result['city_active']){
            echo 'ok<a cityId='.$cityId.' style="color:green !important" href="city.php?type=show&cityId='.$cityId.'">'.$result['city_name'].'</a>' ;
        }else{
            echo 'ok<a cityId='.$cityId.' style="color:rgb(63,81,181) !important" href="city.php?type=show&cityId='.$cityId.'">'.$result['city_name'].'</a>' ;
        }
    }


}else if($action=="addSection" && isset($sectionName) && !empty($sectionName)){
    $result = section_do("addSection",$sectionName);
    if($result == "ok"){
        $row = query("SELECT * FROM `sections` WHERE `active` = 'yes'"); $row = $row->rowCount();
        $last = query("SELECT * FROM `sections` WHERE `active` = 'yes' ORDER BY `section_id` DESC LIMIT 1 ");
        $last = $last->fetch();
        echo 'ok<tr>
                    <td>'.$row.'</td>
                    <td><a href="shopy.php?type=show&sectionId='.$last["section_name"].'">'.$last["section_name"].'</a></td>
                    <td></td>
                    <td>
                        <a style="color:rgb(0,150,136) !important" href="shopy.php?type=show&sectionId='.$last['section_id'].'">نمایش/ویرایش</a>
                        <br>
                        <a class="deleteSection pointer" sectionId="'.$last['section_id'].'" style="color:red !important" >حذف</a>
                    </td>
                </tr>';
    }

}else if($action=="deleteSection" && isset($sectionId) && !empty($sectionId)){
    section_do("delete",$sectionId);
}else if($action=="unActiveCategory" && isset($categoryId) && !empty($categoryId)){
    category_do("unActive",$categoryId);
    echo 'ok';
}else if($action=="activeCategory" && isset($categoryId) && !empty($categoryId)){
    category_do("active",$categoryId);
    echo 'ok';
}else if($action=="unActiveShopCategory" && isset($categoryId) && !empty($categoryId)){
    category_do("unActiveShop",$categoryId);
    echo 'ok';
}else if($action=="activeShopCategory" && isset($categoryId) && !empty($categoryId)){
    category_do("activeShop",$categoryId);
    echo 'ok';
}else if($action=="unActiveShop" && isset($shopId) && !empty($shopId)){
    shop_do("unActive",$shopId);
    echo 'ok';
}else if($action=="activeSection" && isset($sectionId) && !empty($sectionId)){
    section_do("active",$sectionId);
    echo 'ok';
}else if($action=="unActiveSection" && isset($sectionId) && !empty($sectionId)){
    section_do("unActive",$sectionId);
    echo 'ok';
}else if($action=="activeShop" && isset($shopId) && !empty($shopId)){
    shop_do("active",$shopId);
    echo 'ok';
}else if($action=="deleteShopImage" && isset($shopimageid) && !empty($shopimageid)){
    shop_do("deleteShopImage",$shopimageid);
    echo 'ok';
}else if($action=="deleteProductImage" && isset($productImageId) && !empty($productImageId)){
    product_do("deleteProductImage",$productImageId);
    echo 'ok';
}else if($action=="addCategory" && isset($categoryName) && !empty($categoryName)&& isset($sectionId) && !empty($sectionId)){
    $categoryName = cleanInput($categoryName);
    $sectionId = intval($sectionId);
    $result = query("SELECT * FROM `sections_meta` WHERE `section_meta_value` = '$categoryName'  AND `section_meta_key` = 'category' AND `section_meta_section_id` = '$sectionId'");
    if($result->rowCount() >= 1){
        echo 'repeat';
    }else{
        query("INSERT INTO `sections_meta`(`section_meta_section_id`, `section_meta_key`, `section_meta_value`, `meta_active`) VALUES ('$sectionId','category','$categoryName','0')");
        echo "ok";
    }
}else if($action=="addShopCategory" && isset($categoryName) && !empty($categoryName)&& isset($sectionId) && !empty($sectionId)){
    $categoryName = cleanInput($categoryName);
    $sectionId = intval($sectionId);
    $result = query("SELECT * FROM `sections_meta` WHERE `section_meta_value` = '$categoryName' AND `section_meta_key` = 'shop_category'  AND `section_meta_section_id` = '$sectionId'");
    if($result->rowCount() >= 1){
        echo 'repeat';
    }else{
        query("INSERT INTO `sections_meta`(`section_meta_section_id`, `section_meta_key`, `section_meta_value`, `meta_active`) VALUES ('$sectionId','shop_category','$categoryName','0')");
        echo "ok";
    }
}
?>