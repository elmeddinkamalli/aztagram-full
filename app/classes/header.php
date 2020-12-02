<?php
require_once("C:/xampp/htdocs/aztagram/app/path.php");
require_once(ROOT_PATH.'/database/db.php');

class header extends db
{
    public function __construct()
    {
        if(isset($_POST['for'])){
            if($_POST['for'] === 'notifications'){
                $this->getAllNotifications($_SESSION['id']);
            }
        }
    }

    public function haveNotifications(){
        $id = $_SESSION['id'];
        $query = "SELECT not_seen FROM notifications WHERE user_id = $id";
        $stmt = $this->connect()->prepare($query);
        $stmt->execute();
        $record = $stmt->fetch();
        if($record){
            return $record['not_seen'];
        }else{
            return 0;
        }
    }

    private function getAllNotifications($id){
        global $notifications;

        $check_query = "SELECT user_notifications FROM notifications WHERE user_id=$id";
        $check_stmt = $this->connect()->prepare($check_query);
        $check_stmt->execute();
        $record = $check_stmt->fetch();
        if(!$record){
            $query = "INSERT INTO notifications SET user_id = $id, user_notifications = '{}', not_seen = 0";
            $stmt = $this->connect()->prepare($query);
            $stmt->execute();
            $notifications = array();
        }else{
            $notifications = json_decode($record['user_notifications'], true);
        }

        $query = "UPDATE notifications SET not_seen = 0 WHERE user_id=$id";
        $stmt = $this->connect()->prepare($query);
        $stmt->execute();

        if(count($notifications) > 20){
            array_slice($notifications, count($notifications)-21, count($notifications)-1);
        }

        foreach ($notifications as &$n){
            if(isset($n['post_id'])){
                $post_id = $n['post_id'];
                $query = "SELECT image, unique_id FROM posts WHERE id=$post_id";
                $stmt = $this->connect()->prepare($query);
                $stmt->execute();
                $record = $stmt->fetch();
                $n['post_image'] = $record['image'];
                $n['post_unique_id'] = $record['unique_id'];
            }else{
                $username = $n['user'];
                $query = "SELECT avatar FROM users WHERE username='$username'";
                $stmt = $this->connect()->prepare($query);
                $stmt->execute();
                $record = $stmt->fetch();
                $n['user_image'] = $record['avatar'];
                $n['user_username'] = $username;
            }
            
        }

        print_r(json_encode(array_reverse($notifications)));
    }

    public function haveUnseenMessages(){
        $msgs = 0;
        $my_id = $_SESSION['id'];
        $query = "SELECT not_seen_id FROM direct WHERE partner_1 = $my_id OR partner_2 = $my_id";
        $stmt = $this->connect()->prepare($query);
        $stmt->execute();
        $records = $stmt->fetchAll();

        if($records){
            foreach ($records as $record){
                if($record['not_seen_id'] === $my_id){
                    $msgs++;
                }
            }
        }

        return $msgs;
    }
}

$newHeader = new header();