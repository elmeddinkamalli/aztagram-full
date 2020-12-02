<?php
require_once("C:/xampp/htdocs/aztagram/app/path.php");
require_once(ROOT_PATH . '/database/db.php');
class load_new_p extends db
{
    public $new_posts;
    public $loading = false;

    public function __construct()
    {
        if(isset($_POST['last_post_id'])){
            $new_posts = $this->get_data_on_scroll($_POST['last_post_id']);
            echo print_r(json_encode($new_posts), true);
        }
        if(isset($_POST['last_post_id_user'])){
            $new_posts = $this->get_data_on_scroll_user($_POST['last_post_id_user'], $_POST['user_id']);
            echo print_r(json_encode($new_posts), true);
        }
    }

    public function loadData(){
        $load_new_p = new load_new_p();
        $new_posts = $load_new_p->get_data_on_scroll($_GET['last_post_id']);
    }


    public function get_data_on_scroll($last_id)
    {
        $new_posts = array();
        $comments = array();
        $id = $last_id;
        $ids = array();
        $my_id = $_SESSION['id'];

        if (!$this->loading) {
            $this->loading = true;
            
                $following_users = $_SESSION['following_users'];
                $sql = "SELECT posts.*, users.username, users.avatar FROM posts JOIN users ON posts.user_id = users.id WHERE posts.id<=$id AND posts.user_id in ($following_users, $my_id) ORDER BY posts.id DESC LIMIT 2";
                $stmt = $this->connect()->prepare($sql);
                $stmt->execute();
                $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
                array_push($new_posts, $row);
                $new_posts = $new_posts[0];
                //print_r($new_posts);

                foreach($new_posts as &$post){
                    $id = $post['id'];
                    array_push($ids, $id);
                    $sql_for_comments = "SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE comments.post_id=$id ORDER BY comments.id DESC LIMIT 2";
                    $stmt_for_comments = $this->connect()->prepare($sql_for_comments);
                    $stmt_for_comments->execute();
                    $row = $stmt_for_comments->fetchAll(PDO::FETCH_ASSOC);
                    array_push($comments, $row);
                    $post['liked'] = $this->liked_or_not($post['id'], $_SESSION['id']);
                    $post['saved'] = $this->saved_or_not($post['id'], $_SESSION['id']);
                }

                $i = 0;
                foreach($comments as $comment){
                    $comment_count = $this->countComments($ids[$i]);
                    if($comment_count){
                        $new_posts[$i]['comments']['comment_count'] = $comment_count[0]['COUNT(*)'];
                    }
                    $a=1;
                    for($j = 0; $j<count($comment); $j++){
                        $new_posts[$i]['comments']['comment-'.$a]['text'] = $comment[$j]['comment'];
                        $new_posts[$i]['comments']['comment-'.$a]['c_author'] = $comment[$j]['username'];
                        $new_posts[$i]['comments']['comment-'.$a]['comment_id'] = $comment[$j]['id'];
                        $new_posts[$i]['comments']['comment-'.$a]['liked'] = $this->liked_or_not_comment($comment[$j]['id'], $_SESSION['id']);
                        $new_posts[$i]['comments']['comment-'.$a]['like_count'] = $this->liked_or_not_comment($comment[$j]['id'], $_SESSION['id'], 'counter')-1;
                        $a++;
                        if($a === 3){
                            $a = 1;
                        }
                    }
                    $i++;
                }
                
                return $new_posts;
    }
}

    public function get_data_on_scroll_user($last_id, $user_id){
        $new_posts = array();
        $id = $last_id;
        $u_id = $user_id;

        if ($this->loading === false) {
            $this->loading = true;
            $sql = "SELECT id, image, post_likes, unique_id FROM posts WHERE posts.id<=$id AND user_id=$u_id ORDER by posts.id DESC LIMIT 6";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute();
            $row = $stmt->fetchAll();
            
            array_push($new_posts, $row);
            $new_posts = $new_posts[0];

            $i = 0;
            foreach($row as $p){
                $get_comment_count = $this->countComments($p['id']);
                $new_posts[$i]['comment_count'] = $get_comment_count[0]['COUNT(*)'];
                $new_posts[$i]['idenity'] = rand(10, 10000);
                $i++;
            }

        $this->loading = false;
        return $new_posts;
        }
    }

    public function countComments($post_id, $counter = 'no'){
        $sql_for_comments_count = "SELECT COUNT(*) FROM comments WHERE post_id=$post_id";
        $stmt_for_comments_count = $this->connect()->prepare($sql_for_comments_count);
        $stmt_for_comments_count->execute();
        $count = $stmt_for_comments_count->fetchAll();
        return $count;
    }

    private function liked_or_not($post_id, $user_id){
        $query = "SELECT liked_users FROM likes WHERE post_id=$post_id";
        $stmt = $this->connect()->prepare($query);
        $stmt->execute();
        $record = $stmt->fetch();
        if($record){
            $str_to_array = explode(',',$record['liked_users']);
            if(in_array($user_id, $str_to_array)){
                return 'yes';
            }else{
                return 'no';
            }
        }else{
            return 'no';
        }
    }

    private function saved_or_not($post_id, $user_id){
        $query = "SELECT post_id FROM saves WHERE user_id=$user_id";
        $stmt = $this->connect()->prepare($query);
        $stmt->execute();
        $record = $stmt->fetch();
        if($record){
            $str_to_array = explode(',',$record['post_id']);

            if(in_array($post_id, $str_to_array)){
                return 'yes';
            }else{
                return 'no';
            }
        }else{
            return 'no';
        }
    }

    private function liked_or_not_comment($comment_id, $user_id, $for = 'liked_or_not'){
        $query = "SELECT liked_users FROM comments WHERE id=$comment_id";
        $stmt = $this->connect()->prepare($query);
        $stmt->execute();
        $record = $stmt->fetch();
        $str_to_array = explode(',',$record['liked_users']);
        if($for === 'counter'){
            return count($str_to_array);
        }
        if($record){
            if(in_array($user_id, $str_to_array)){
                return 'yes';
            }else{
                return 'no';
            }
        }else{
            return 'no';
        }
    }
}

$newload = new load_new_p();