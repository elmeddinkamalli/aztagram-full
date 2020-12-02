<?php

require_once("C:/xampp/htdocs/aztagram/app/path.php");
require_once(ROOT_PATH.'/database/db.php');

class post_actions extends db{
    public $loading = false;

    public function __construct(){
        if(isset($_POST['post_id'])){
            if($_POST['for'] === 'heart'){
                $this->heartPost($_POST['post_id']);
            }else if($_POST['for'] === 'comment'){
                $this->addComment($_POST['comment'], $_POST['post_id']);
            }else if($_POST['for'] === 'save'){
                $this->savePost($_POST['post_id']);
            }else if($_POST['for'] === 'show_likes'){
                $this->showLikes($_POST['post_id']);
            }
        }

        if(isset($_POST['comment_like_unlike'])){
            $this->likeUnlikeComment($_POST['comment_id']);
        }

        if(isset($_FILES)){
            $this->checkUploadedFile($_FILES);
        }

        if(isset($_POST['image'])){
            $this->createNewPost($_POST['my_id'], $_POST['description'], $_POST['image']);
        }
    }

    //Methods
    private function heartPost($post_id){
        if(!$this->loading){
            $this->loading = true;

            $my_id = $_SESSION['id'];
            $check_query = "SELECT * FROM likes WHERE post_id=$post_id";
            $check_stmt = $this->connect()->prepare($check_query);
            $check_stmt->execute();
            $record = $check_stmt->fetch();

            $user_query = "SELECT user_id FROM posts WHERE id=$post_id";
            $user_stmt = $this->connect()->prepare($user_query);
            $user_stmt->execute();
            $user_id = $user_stmt->fetch();
            if($record){
                $str_to_array = explode(',',$record['liked_users']);
                $new_like_count = 0;
                if(!in_array($my_id, $str_to_array)){
                    $my_id = ','.$my_id;
                    $query = "UPDATE likes SET liked_users = CONCAT(liked_users, '$my_id') WHERE post_id=$post_id";
                    $stmt = $this->connect()->prepare($query);
                    $stmt->execute();
                    echo "liked";
                    $new_like_count = count($str_to_array);
                    $this->inc_dec_like_count($new_like_count, $post_id);
                    $this->loading = false;
                    $this->addNotification($post_id, 'like', $user_id['user_id']);
                }else{
                    $delete_user = array_diff($str_to_array, [$my_id]);
                    $ready = implode(',', $delete_user);
                    $query = "UPDATE likes SET liked_users = '$ready' WHERE post_id=$post_id";
                    $stmt = $this->connect()->prepare($query);
                    $stmt->execute();
                    echo "dissliked";
                    $new_like_count = count($str_to_array);
                    $this->inc_dec_like_count($new_like_count-2, $post_id);
                    $this->loading = false;
                    $this->addNotification($post_id, 'unlike', $user_id['user_id']);
                }
            }else{
                $my_id = ','.$my_id;
                $query = "INSERT INTO likes SET post_id = $post_id, liked_users = '$my_id'";
                $stmt = $this->connect()->prepare($query);
                $stmt->execute();
                echo "liked";
                $this->inc_dec_like_count(1, $post_id);
                $this->loading = false;
                $this->addNotification($post_id, 'like', $user_id['user_id']);
            }
        }else{
            return false;
        }
    }

    private function inc_dec_like_count($count, $post_id){
        $query = "UPDATE posts SET post_likes=$count WHERE id=$post_id";
        $stmt = $this->connect()->prepare($query);
        $stmt->execute();
    }

    private function addComment($comment, $post_id){
        if(!$this->loading){
            $this->loading = true;
            $rand_idenity = rand(100, 2147483647);
            $my_id = $_SESSION['id'];
            $query = "INSERT INTO comments SET user_id=$my_id, post_id=$post_id, comment = ?, rand_id=$rand_idenity";
            $stmt = $this->connect()->prepare($query);
            $stmt->execute(array($comment));
            $query_c = "SELECT * FROM comments WHERE rand_id=$rand_idenity LIMIT 1";
            $stmt_c = $this->connect()->prepare($query_c);
            $stmt_c->execute();
            $record = $stmt_c->fetch(PDO::FETCH_ASSOC);
            $record['author'] = $_SESSION['username'];
            print_r(json_encode($record));
            $this->loading = false;

            $query = "SELECT user_id FROM posts WHERE id=$post_id";
            $stmt = $this->connect()->prepare($query);
            $stmt->execute();
            $userId = $stmt->fetch();
            $this->addNotification($post_id, 'comment', $userId['user_id']);
        }else{
            $this->loading = false;
            return false;
        }
    }

    private function savePost($post_id){
        if(!$this->loading){
            $this->loading = true;

            $my_id = $_SESSION['id'];
            $check_query = "SELECT * FROM saves WHERE user_id=$my_id";
            $check_stmt = $this->connect()->prepare($check_query);
            $check_stmt->execute();
            $record = $check_stmt->fetch();
            if($record){
                $str_to_array = explode(',',$record['post_id']);
                if(!in_array($post_id, $str_to_array)){
                    $post_id = ','.$post_id;
                    $query = "UPDATE saves SET post_id = CONCAT(post_id, '$post_id') WHERE user_id=$my_id";
                    $stmt = $this->connect()->prepare($query);
                    $stmt->execute();
                    echo "saved";
                    $this->loading = false;
                }else{
                    $delete_post = array_diff($str_to_array, [$post_id]);
                    $ready = implode(',', $delete_post);
                    $query = "UPDATE saves SET post_id = '$ready' WHERE user_id=$my_id";
                    $stmt = $this->connect()->prepare($query);
                    $stmt->execute();
                    echo "not-saved";
                    $this->loading = false;
                }
            }else{
                $post_id = ','.$post_id;
                $query = "INSERT INTO saves SET user_id = $my_id, post_id = '$post_id'";
                $stmt = $this->connect()->prepare($query);
                $stmt->execute();
                echo "saved";
                $this->loading = false;
            }
        }else{
            return false;
        }
    }

    private function likeUnlikeComment($comment_id){
        if(!$this->loading){
            $this->loading = true;

            $my_id = $_SESSION['id'];
            $check_query = "SELECT liked_users FROM comments WHERE id=$comment_id";
            $check_stmt = $this->connect()->prepare($check_query);
            $check_stmt->execute();
            $record = $check_stmt->fetch();

            if($record){
                $str_to_array = explode(',',$record['liked_users']);
                if(!in_array($my_id, $str_to_array)){
                    $my_id = ','.$my_id;
                    $query = "UPDATE comments SET liked_users = CONCAT(liked_users, '$my_id') WHERE id=$comment_id";
                    $stmt = $this->connect()->prepare($query);
                    $stmt->execute();
                    echo "liked";
                    $this->loading = false;
                }else{
                    $delete_like = array_diff($str_to_array, [$my_id]);
                    $ready = implode(',', $delete_like);
                    $query = "UPDATE comments SET liked_users = '$ready' WHERE id=$comment_id";
                    $stmt = $this->connect()->prepare($query);
                    $stmt->execute();
                    echo "unliked";
                    $this->loading = false;
                }
            }else{
                $this->loading = false;
                return false;
            }
        }
    }

    private function showLikes($post_id){
        $users = array();

        $query = "SELECT liked_users FROM likes WHERE post_id=$post_id";
        $stmt = $this->connect()->prepare($query);
        $stmt->execute();
        $record = $stmt->fetch();
        $users_ids = $record['liked_users'];

        $followers_query = "SELECT id,name,username,avatar FROM users WHERE id in (0 $users_ids)";
        $stmt = $this->connect()->prepare($followers_query);
        $stmt->execute();
        $record = $stmt->fetchAll(PDO::FETCH_ASSOC);
        array_push($users, $record);
        $users = $users[0];
        foreach($users as &$user){
            $user['following'] = $this->followed_or_not($_SESSION['following_users'], $user['id']);
        }
        print_r(json_encode($users));
    }

    public static function followed_or_not($my_follows, $id){
        $str_to_array = explode(',', $my_follows);
        if(in_array((string)$id, $str_to_array)){
            return true;
        }else{
            return false;
        }
    }

    private function checkUploadedFile($formData){
        if(isset($formData['file'])){
            $fileName = time() . '_' . strtolower($formData['file']['name']);
            $location = $_SERVER['DOCUMENT_ROOT']."/aztagram/people/images/".$fileName;
            $imageFileType = pathinfo($location, PATHINFO_EXTENSION);
            $valid_extension = array('jpg', 'jpeg', 'png');
            if(!in_array(strtolower($imageFileType), $valid_extension)){
                echo 'Extension-error';
            }else{
                if(move_uploaded_file($formData['file']['tmp_name'], $location)){
                    echo $fileName;
                }else{
                    echo 'error';
                }
            }
        }
    }

    private function createNewPost($my_id, $description, $image){
        $query = "INSERT INTO posts SET user_id=$my_id, description = ?, image='$image',
                unique_id=CONCAT(SUBSTRING('ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789', RAND()*34+1, 1),
                SUBSTRING('ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789', RAND()*34+1, 1),
                SUBSTRING('ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789', RAND()*34+1, 1),
                SUBSTRING('ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789', RAND()*34+1, 1),
                SUBSTRING('ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789', RAND()*34+1, 1),
                SUBSTRING('ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789', RAND()*34+1, 1),
                SUBSTRING('ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789', RAND()*34+1, 1),
                SUBSTRING('ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789', RAND()*34+1, 1)
                )";
        $stmt = $this->connect()->prepare($query);
        $stmt->execute(array($description));
        echo "shared";
    }

    //Update Notifications
    private function addNotification($post_id, $for, $data = null){
        if($_SESSION['id'] !== $data){
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
    
    
                if($for == 'like'){
                    $message['post_id'] = $post_id;
                    $message['user'] = $_SESSION['username'];
                    $message['msg'] = 'liked your photo';
                    array_push($notifications, $message);
                    $notifications = json_encode($notifications);
    
                    $query = "UPDATE notifications SET not_seen = not_seen + 1, user_notifications = '$notifications' WHERE user_id = $data";
                    $stmt = $this->connect()->prepare($query);
                    $stmt->execute();
                }else if($for == 'unlike'){
                    $message['post_id'] = $post_id;
                    $message['user'] = $_SESSION['username'];
                    $message['msg'] = 'liked your photo';
                    foreach ($notifications as $i => $value){
                        if($value == $message){
                            unset($notifications[$i]);
                        }
                    }
                    $notifications = json_encode($notifications);
                    $query = "UPDATE notifications SET not_seen = not_seen - 1, user_notifications = '$notifications' WHERE user_id = $data";
                    $stmt = $this->connect()->prepare($query);
                    $stmt->execute();
                }else if($for == 'comment'){
                    $message['post_id'] = $post_id;
                    $message['user'] = $_SESSION['username'];
                    $message['msg'] = 'commented to your photo';
                    array_push($notifications, $message);
                    $notifications = json_encode($notifications);
    
                    $query = "UPDATE notifications SET not_seen = not_seen + 1, user_notifications = '$notifications' WHERE user_id = $data";
                    $stmt = $this->connect()->prepare($query);
                    $stmt->execute();
                }
            }
        }
        $this->loading = false;
    }
}

$newPostActions = new post_actions();