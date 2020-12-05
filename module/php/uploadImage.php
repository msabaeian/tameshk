<?php
if(@$_FILES['file']['error']>0){
    echo 'Something is not true in upload';
}else if(!empty($_FILES['file'])){
    $tmp = $_FILES['file']['tmp_name'];
    $name = cleanInput($_FILES['file']['name']);
    $img_type = $_FILES['file']['type'];
    if(is_uploaded_file($tmp)){
        $whitelist = array("image/jpg","image/jpeg","image/png");
        $data = date("Y-m-d-H-is",time());
        if(in_array($img_type,$whitelist)){
            $name = 'image-'.$data.'--'.$name;
            $add = 'uploads/'.$name;
            $move = move_uploaded_file($tmp,"../".$add);
            if($move){
                unset($add,$move,$tmp,$img_type);
            }else{
                echo 'an error in uploading';
            }
        }else{
            echo "your file's format is not suported";
        }
    }else{
        echo 'What are you doing?';
    }
}
?>