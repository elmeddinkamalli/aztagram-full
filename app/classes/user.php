<?php

require_once("C:/xampp/htdocs/aztagram/app/path.php");
require_once(ROOT_PATH.'/database/db.php');

class user extends db
{
    public function __construct()
    {
        if(isset($_FILES)){
            $this->checkUploadedFile($_FILES);
        }
    }

    private function checkUploadedFile($formData){
        if(isset($formData['file'])){
            $fileName = time() . '_' . strtolower($formData['file']['name']);
            $location = $_SERVER['DOCUMENT_ROOT']."/aztagram/people/profile-pics/".$fileName;
            $imageFileType = pathinfo($location, PATHINFO_EXTENSION);
            $valid_extension = array('jpg', 'jpeg', 'png');
            if(!in_array(strtolower($imageFileType), $valid_extension)){
                echo 'Extension-error';
            }else{
                if(move_uploaded_file($formData['file']['tmp_name'], $location)){
                    $my_id = $_SESSION['id'];
                    $sql = "UPDATE users SET avatar='$fileName' WHERE id=$my_id";
                    $stmt = $this->connect()->prepare($sql);
                    $stmt->execute();
                    $_SESSION['avatar'] = $fileName;
                }else{
                    echo 'error';
                }
            }
        }
    }
}

$newUserCode = new user();