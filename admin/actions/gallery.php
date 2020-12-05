<?php
include 'autoload.php';
include  '../includes/admin_info.php';
permission("gallery",true);
@$action = $_POST['action'];
if($action=="reject" && isset($GalleryId) && !empty($GalleryId)){
    gallery_do("reject",intval($GalleryId));
    echo "ok";
}else if($action=="accept" && isset($GalleryId) && !empty($GalleryId)){
    gallery_do("accept",intval($GalleryId));
    echo "ok";
}

function gallery_do($type,$c_id,$data = ''){
    global $admin_id;
    switch ($type){
        case "reject":
            $c_id = intval($c_id);
            $comment['image_status'] = -1;
            $comment['image_active_by'] = $admin_id; // ADMIN ID
            $comment['image_date'] = get_date(); // ADMIN ID
            query(queryUpdate('gallery',$comment,'WHERE `image_id` = '.$c_id.';'));
            break;
        case "accept":
            $c_id = intval($c_id);
            $comment['image_status'] = 1;
            $comment['image_active_by'] = $admin_id; // ADMIN ID
            $comment['image_date'] = get_date(); // ADMIN ID
            query(queryUpdate('gallery',$comment,'WHERE `image_id` = '.$c_id.';'));
            break;
    }
}
?>