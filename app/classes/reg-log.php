<?php
require_once("C:/xampp/htdocs/aztagram/app/path.php");
require_once(ROOT_PATH.'/database/db.php');
class reg_log extends db
{
    public $loading = false;
    public $errors = array();
    public $email, $username, $full_name, $username_or_email;
    public $user_by_get = array();

    public function __construct(){
        if(isset($_POST['sign-up'])){
            $this->register($_POST);
        }
        if(isset($_POST['update_user_btn'])){
            $this->updateUser($_POST);
        }
        if(isset($_POST['update_password'])){
            $this->updatePassword($_POST);
        }
        if(isset($_POST['log-in'])){
            $this->checkLogin($_POST);
        }
        if(isset($_GET['user'])){
            $this->getUserByGet($_GET['user']);
        }
        if(isset($_POST['my_id']) || isset($_POST['user_id'])){
            if($_POST['for'] === "follow"){
                $this->follow($_POST['my_id'], $_POST['user_id']);
            }else if($_POST['for'] === "unfollow"){
                $this->unfollow($_POST['my_id'], $_POST['user_id']);
            }else if($_POST['for'] === "saves"){
                $this->getSaves($_POST['my_id']);
            }else if($_POST['for'] === "show_followings"){
                $this->getFollowings($_POST['user_id']);
            }else if($_POST['for'] === "show_followers"){
                $this->getFollowers($_POST['user_id']);
            }
        }
        if(isset($_SESSION['username']) && isset($_SESSION['id']) && !isset($_GET['user'])){
            $this->getUserByGet($_SESSION['username'], true);
        }
    }

    //methods
    private function register($data){
        $this->validateUser($data);

        if(count($this->errors) === 0){
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

            unset($data['sign-up']);
            $sql = "INSERT INTO users SET ";

            $i = 0;
            foreach($data as $key => $value){
                if($i === 0){
                    $sql = $sql . " $key=?";
                }else{
                    $sql = $sql . ", $key=?";
                }
                $i++;
            }

            if($stmt = $this->executeQuery($sql, $data)){
                $user = $this->selectUser(['username' => $data['username']]);
                $this->login($user);
            }
        }else{
            $this->email = $data['email'];
            $this->username = $data['username'];
            $this->full_name = $data['name'];
        }
    }

    private function checkLogin($data){
        $this->validateLogin($data);

        if(count($this->errors) === 0){
            $user_with_username = $this->selectUser(['username' => $data['username_or_email']]);
            $user_with_email = $this->selectUser(['email' => $data['username_or_email']]);

            if($user_with_username && password_verify($data['password'], $user_with_username['password'])){
                $this->login($user_with_username);
            }else if($user_with_email && password_verify($data['password'], $user_with_email['password'])){
                $this->login($user_with_email);
            }else{
                $this->username_or_email = $data['username_or_email'];
                array_push($this->errors, 'Wrong credentials!');
            }
        }
    }

    private function login($user){
        $_SESSION['id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['bio'] = $user['bio'];
        $_SESSION['avatar'] = $user['avatar'];
        $_SESSION['following_users'] = $user['following_users'];
    }

    private function getUserByGet($username, $editPage = false){
        $user = $this->selectUser(['username' => $username]);

        if($user){
            array_push($this->user_by_get, $user);
            $this->user_by_get = $this->user_by_get[0];
            $this->user_by_get['post_count'] = $this->posts_followers_unfs_count($this->user_by_get['id'], 'post_count');
            $this->user_by_get['following_users'] = $this->posts_followers_unfs_count($this->user_by_get['id'], 'following_users');
            $this->user_by_get['followers'] = $this->posts_followers_unfs_count($this->user_by_get['id'], 'followers');
            unset($this->user_by_get['password']);
            if(!$editPage) {
                unset($this->user_by_get['email']);
            }
        }
    }

    private function executeQuery($sql, $data){
        $stmt = $this->connect()->prepare($sql);
        $values = array_values($data);
        $stmt->bindValue("?", $values, PDO::PARAM_INT);
        $stmt->execute($values);
        return $stmt;
    }

    private function validateUser($user, $update = false){
        if(empty($user['email'])){
            array_push($this->errors, "Email required!");
        }else if (!filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
            array_push($this->errors, "Invalid email!");
        }
        if(empty($user['username'])){
            array_push($this->errors, "Username required!");
        }else if(strlen($user['username']) < 5){
            array_push($this->errors, "Username has to contain more than 4 characters!");
        }else if(strlen($user['username']) > 20){
            array_push($this->errors, "Username is too long!");
        }else if(preg_match('/\s/', $user['username'])){
            array_push($this->errors, "Username cannot contain whitespaces!");
        }else if(!preg_match('/^[A-Za-z0-9\_]+$/', $user['username'])){
            array_push($this->errors, "Username contains only letters, numbers and underline!");
        }
        if(empty($user['name'])){
            array_push($this->errors, "Full name required!");
        }else if(!preg_match('/[A-Za-z0-9\_\-]+/', $user['name'])){
            array_push($this->errors, "Unsupported characters provided in full name!");
        }
        if(!$update){
            if(empty($user['password'])){
                array_push($this->errors, "Password required!");
            }else if(strlen($user['password']) < 8){
                array_push($this->errors, "Password has to contain more than 7 characters!");
            }
        }

        $exEmail = $this->selectUser(['email' => $user['email']]);
        $exUsername = $this->selectUser(['username' => $user['username']]);

        if(!$update){
            if($exEmail){
                array_push($this->errors, 'Email already exist');
            }
            if($exUsername){
                array_push($this->errors, 'Username already exist');
            }
        }else{
            if($exEmail){
                if($exEmail['id'] !== $_SESSION['id']){
                    array_push($this->errors, 'Email already exist');
                }
            }
            if($exUsername){
                if($exUsername['id'] !== $_SESSION['id']){
                    array_push($this->errors, 'Username already exist');
                }
            }
        }

        return $this->errors;
    }

    private function validateLogin($user){
        if(empty($user['username_or_email'])){
            array_push($this->errors, "Username or email required!");
        }
        if(empty($user['password'])){
            array_push($this->errors, "Password required!");
        }

        return $this->errors;
    }

    private function selectUser($condition){
        $sql = "SELECT * FROM users ";

        $i = 0;
        foreach($condition as $key => $value){
            if($i === 0){
                $sql = $sql . "WHERE $key=?";
            }else{
                $sql = $sql . " AND $key=?";
            }
            $i++;
        }

        $sql = $sql . " LIMIT 1";
        $stmt = $this->executeQuery($sql, $condition);
        $records = $stmt->fetch(PDO::FETCH_ASSOC);
        return $records;
    }

    private function updateUser($data){
        $this->validateUser($data, true);
        $user_id = $_SESSION['id'];

        if(count($this->errors) === 0){
            unset($data['update_user_btn']);
            $sql = "UPDATE users SET ";

            $i = 0;
            foreach($data as $key => $value){
                if($i === 0){
                    $sql = $sql . " $key=?";
                }else{
                    $sql = $sql . ", $key=?";
                }
                $i++;
            }
            $sql = $sql . " WHERE id=$user_id";

            if($stmt = $this->executeQuery($sql, $data)){
                $user = $this->selectUser(['username' => $data['username']]);
                $this->login($user);
            }
        }
    }

    private function updatePassword($data){
        if(empty($data['old_pass'])){
            array_push($this->errors, "Old password required!");
        }
        if(empty($data['new_pass'])){
            array_push($this->errors, "New password required!");
        }else if(strlen($data['new_pass']) < 8){
            array_push($this->errors, "Password has to contain more than 7 characters!");
        }
        if(empty($data['confirm_new_pass'])){
            array_push($this->errors, "Confirm new password!");
        }else if($data['new_pass'] !== $data['confirm_new_pass']){
            array_push($this->errors, "Confirmation is wrong!");
        }
        if(count($this->errors) === 0){
            $user = $this->selectUser(['id' => $_SESSION['id']]);
            if(password_verify($data['old_pass'], $user['password'])){
                if(password_verify($data['new_pass'], $user['password'])){
                    array_push($this->errors, "Provide different password, please.");
                }else{
                    $new_pass = password_hash($data['new_pass'], PASSWORD_DEFAULT);
                    $user_id = $_SESSION['id'];

                    $sql = "UPDATE users SET password='$new_pass' WHERE id=$user_id";
                    $stmt = $this->connect()->prepare($sql);
                    $stmt->execute();
                }
            }else{
                array_push($this->errors, "Old password is wrong!");
            }
        }
    }

    public static function followed_or_not($my_follows, $id){
        $str_to_array = explode(',', $my_follows);
        if(in_array((string)$id, $str_to_array)){
            return true;
        }else{
            return false;
        }
    }

    private function follow($my_id, $user_id){
        $quoted_user_id = ','.$user_id;
        $query = "UPDATE users SET following_users = CONCAT(following_users, '$quoted_user_id') WHERE id=$my_id";
        $stmt = $this->connect()->prepare($query);
        $stmt->execute();
        $_SESSION['following_users'] = $_SESSION['following_users'] . $quoted_user_id;

        $quoted_my_id = ','.$my_id;
        $followers_query = "UPDATE users SET followers = CONCAT(followers, '$quoted_my_id') WHERE id=$user_id";
        $followers_stmt = $this->connect()->prepare($followers_query);
        $followers_stmt->execute();
        //$_SESSION['following_users'] = $_SESSION['following_users'] . $user_id;

        $this->addNotification($my_id, 'follow', $user_id);
    }

    private function unfollow($my_id, $user_id){
        $query = "SELECT following_users FROM users WHERE id=$my_id";
        $stmt = $this->connect()->prepare($query);
        $stmt->execute();
        $string = $stmt->fetch();
        $str_to_array = explode(',',$string['following_users']);
        $delete_user = array_diff($str_to_array, [$user_id]);
        $ready = implode(',', $delete_user);
        $query = "UPDATE users SET following_users = '$ready' WHERE id=$my_id";
        $stmt = $this->connect()->prepare($query);
        $stmt->execute();
        $_SESSION['following_users'] = $ready;

        $query = "SELECT followers FROM users WHERE id=$user_id";
        $stmt = $this->connect()->prepare($query);
        $stmt->execute();
        $string = $stmt->fetch();
        $str_to_array = explode(',',$string['followers']);
        $delete_user = array_diff($str_to_array, [$my_id]);
        $ready = implode(',', $delete_user);
        $query = "UPDATE users SET followers = '$ready' WHERE id=$user_id";
        $stmt = $this->connect()->prepare($query);
        $stmt->execute();

        $this->addNotification($my_id, 'unfollow', $user_id);
    }

    private function getSaves($my_id){
        if(!$this->loading){
            $this->loading = true;

            $saves = array();
            $query = "SELECT post_id FROM saves WHERE user_id=$my_id";
            $stmt = $this->connect()->prepare($query);
            $stmt->execute();
            $records = $stmt->fetch();

            if($records){
                $str_to_array = explode(',',$records['post_id']);
                if($str_to_array[0] === ""){
                    unset($str_to_array[0]);
                }
                $ready = implode(',',$str_to_array);

                $saves_query = "SELECT id, image, post_likes, unique_id FROM posts WHERE id in ($ready)";
                $saves_stmt = $this->connect()->prepare($saves_query);
                $saves_stmt->execute();
                $saves_records = $saves_stmt->fetchAll(PDO::FETCH_ASSOC);
                array_push($saves, $saves_records);
                $saves = $saves[0];
                $i=0;
                foreach($saves as &$post){
                    $saves[$i++]['comment_count'] = $this->countComments($post['id']);
                }
                echo print_r(json_encode($saves), true);
                $this->loading = false;
            }else{
                $this->loading = false;
                return false;
            }
        }
    }

    private function countComments($post_id){
        $sql_for_comments_count = "SELECT COUNT(*) FROM comments WHERE post_id=$post_id";
        $stmt_for_comments_count = $this->connect()->prepare($sql_for_comments_count);
        $stmt_for_comments_count->execute();
        $count = $stmt_for_comments_count->fetchAll();
        return $count[0]['COUNT(*)'];
    }

    private function posts_followers_unfs_count($user_id, $for){
        if($for === 'post_count'){
            $sql = "SELECT COUNT(id) FROM posts WHERE user_id=$user_id";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute();
            $count = $stmt->fetchAll();
            return $count[0]['COUNT(id)'];
        }else{
            $sql = "SELECT $for FROM users WHERE id=$user_id";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute();
            $records = $stmt->fetch();
            $str_to_array = explode(',',$records[$for]);
            $count = count($str_to_array);
            if($count === 1){
                return 0;
            }else{
                return $count-1;
            }
        }
    }

    private function getFollowings($user_id){
        $users = array();

        $query = "SELECT following_users FROM users WHERE id=$user_id";
        $stmt = $this->connect()->prepare($query);
        $stmt->execute();
        $record = $stmt->fetch();
        $users_ids = $record['following_users'];
        
        $following_query = "SELECT id,name,username,avatar FROM users WHERE id in ($users_ids)";
        $stmt = $this->connect()->prepare($following_query);
        $stmt->execute();
        $record = $stmt->fetchAll(PDO::FETCH_ASSOC);
        array_push($users, $record);
        $users = $users[0];
        foreach($users as &$user){
            $user['following'] = $this->followed_or_not($_SESSION['following_users'], $user['id']);
        }
        print_r(json_encode($users));
        $users = array();
    }

    private function getFollowers($user_id){
        $users = array();

        $query = "SELECT followers FROM users WHERE id=$user_id";
        $stmt = $this->connect()->prepare($query);
        $stmt->execute();
        $record = $stmt->fetch();
        $users_ids = $record['followers'];
        
        $followers_query = "SELECT id,name,username,avatar FROM users WHERE id in ($users_ids)";
        $stmt = $this->connect()->prepare($followers_query);
        $stmt->execute();
        $record = $stmt->fetchAll(PDO::FETCH_ASSOC);
        array_push($users, $record);
        $users = $users[0];
        foreach($users as &$user){
            $user['following'] = $this->followed_or_not($_SESSION['following_users'], $user['id']);
        }
        print_r(json_encode($users));
        $users = array();
    }

    //Update Notifications
    private function addNotification($my_id, $for, $data = null){
        if(!$this->loading){
            $this->loading = true;
            global $notifications;
            global $message;

            $check_query = "SELECT * FROM notifications WHERE user_id=$data";
            $check_stmt = $this->connect()->prepare($check_query);
            $check_stmt->execute();
            $record = $check_stmt->fetch();
            if(!$record){
                $query = "INSERT INTO notifications SET user_id = $data, user_notifications = '{}', not_seen = 0";
                $stmt = $this->connect()->prepare($query);
                $stmt->execute();
                $notifications = array();
            }else{
                $notifications = json_decode($record['user_notifications'], true);
            }


            if($for == 'follow'){
                //$message['followed_user'] = $my_id;
                $message['user'] = $_SESSION['username'];
                $message['msg'] = ' followed you';
                array_push($notifications, $message);
                $notifications = json_encode($notifications);

                $query = "UPDATE notifications SET not_seen = not_seen + 1, user_notifications = '$notifications' WHERE user_id = $data";
                $stmt = $this->connect()->prepare($query);
                $stmt->execute();

            }else if($for == 'unfollow'){
                //$message['followed_user'] = $my_id;
                $message['user'] = $_SESSION['username'];
                $message['msg'] = " followed you";
                foreach ($notifications as $i => $value){
                    if($value == $message){
                        unset($notifications[$i]);
                    }
                }
                $notifications = json_encode($notifications);
                $query = "UPDATE notifications SET not_seen = not_seen - 1, user_notifications = '$notifications' WHERE user_id = $data";
                $stmt = $this->connect()->prepare($query);
                $stmt->execute();

            }
        }
        $this->loading = false;
    }
}

$user_here = new reg_log();