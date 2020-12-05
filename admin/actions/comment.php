<?php
include 'autoload.php';
include  '../includes/admin_info.php';
permission("comment",true);
@$action = $_POST['action'];
if($action=="reject" && isset($commentId) && !empty($commentId)){
    comment_do("reject",intval($commentId));
    echo "ok";
}else if($action=="accept" && isset($commentId) && !empty($commentId)){
    comment_do("accept",intval($commentId));
    echo "ok";
}
?>