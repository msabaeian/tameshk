<?php
if(isset( $_SESSION['user_id_admin_panel'])){
    $admin_id =  intval($_SESSION['user_id_admin_panel']);
    $result = query("SELECT * FROM `users_meta` WHERE `users_meta_user_id` = ".$admin_id);
    foreach ($result as $pr=>$key){
        $$key['users_meta_key'] = cleanInput($key['users_meta_value']);
    }

    $result = query("SELECT * FROM `users` WHERE `users_ID` = ".$admin_id);
    $result = $result->fetch();
    $admin_display_name = $result['user_display_name'];
    $user_phone = $result['user_phone'];
    $user_status = intval($result['user_status']);

    $page_names = array(
        "dashboard" => "داشبورد",
        "notes" => "یادداشت ها",
        "cities" => "شهر ها",
        "online-cities" => "همه شهر ها",
        "add-city" => "افزودن شهر",
        "shopy" => "فروشگاهی",
        "sections" => "بخش ها",
        "add-section" => "افزودن بخش",
        "shops" => "فروشگاه ها",
        "add-shop" => "افزودن فروشگاه",
        "products" => "محصولات",
        "add-product" => "افزودن محصول",
        "hot-products" => "پیشنهادات داغ",
        "user" => "کاربری",
        "users" => "کاربران",
        "blocked-users" => "کاربران مسدود شده",
        "add-user" => "افزودن کاربر",
        "comment" => "دیدگاه",
        "new-comments" => "دیدگاه های جدید",
        "approved-comments" => "دیدگاه های تایید شده",
        "rejected-comments" => "دیدگاه های رد شده",
        "delivery" => "پیک",
        "delivery-centers" => "مراکز",
        "delivery-add-centers" => "افزودن مرکز",
        "delivery-driver" => "افزودن راننده",
        "delivery-online" => "پیک آنلاین",
        "discount" => "تخفیف",
        "add-discount" => "افزودن کد تخفیف",
        "all-discount" => "نمایش همه",
        "order" => "سفارشات",
        "all-order" => "همه سفارشات",
        "done-order" => "سفارشات انجام شده",
        "undone-order" => "سفارشات انجام نشده",
        "payments" => "پرداخت ها",
        "all-payments" => "همه پرداخت",
        "done-payments" => "پرداخت انجام شده",
        "undone-payments" => "پرداخت انجام نشده",
        "gallery" => "گالری",
        "new-gallery" => " تصاویر جدید",
        "accept-gallery" => "تصاویر تایید شده",
        "reject-gallery" => "تصاویر رد شده"

    );

    $result = query("SELECT * FROM `users_meta` WHERE `users_meta_key` = 'permission' AND `users_meta_user_id` = ".$admin_id);
    $user_permission = array();
    if($result->rowCount() >= 1){
        foreach ($result as $pr){
            array_push($user_permission,@$pr['users_meta_value']);
        }
    }


}
?>