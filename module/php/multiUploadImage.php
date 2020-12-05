<?php
if(!empty($_FILES['file'])){
    $fileNames = array();
    $size = $_FILES['file']['name'];
    $whitelist = array("image/jpg","image/jpeg","image/png");
    for($i=0;$i<=(sizeof($size)-1);$i++){
        $tmp = $_FILES['file']['tmp_name'][$i];
        $name = $_FILES['file']['name'][$i];
        $typef = $_FILES['file']['type'][$i];
        if(is_uploaded_file($tmp)){
            $data = date("Y-m-d-H-is",time());
            if(in_array($typef,$whitelist)){
                $name = 'image-'.$data.'--'.$name;
                $add = 'uploads/'.$name;
                //$add = $name;
                $move = move_uploaded_file($tmp,"../".$add);
                //$move = move_uploaded_file($tmp,$add);
                if($move){
                    array_push($fileNames,$name);

                }else{
                    echo 'an error in uploading';
                }
            }else{
                echo "your file's format is not suported";
            }
        }
    }
    //unset($fileNames[0]);
    //$fileNames = json_encode(array_values($fileNames));
    /*
    $tmp = $_FILES['file']['tmp_name'];
    $name = $_FILES['file']['name'];
    $type = $_FILES['file']['type'];
    $folderName = cleanInput($_POST['image_folder']);
    if(is_uploaded_file($tmp)){
        $whitelist = array("image/jpg","image/jpeg","image/png");
        $data = date("Y-m-d-H-is",time());
        if(in_array($type,$whitelist)){
            $name = $folderName.'-'.$data.'--'.$name;
            $add = 'uploads/'.$folderName.'/'.$name;
            $move = move_uploaded_file($tmp,"../".$add);
            if($move){

            }else{
                echo 'an error in uploading';
            }
        }else{
            echo "your file's format is not suported";
        }
    }else{
        echo 'What are you doing?';
    }
    */
}
?>