<?php
require_once("C:/xampp/htdocs/aztagram/app/path.php");
require_once(ROOT_PATH . '/database/db.php');

class load_new_c extends db
{
    public $comments;

    public function __construct()
    {
        if(isset($_GET['post_id'])){
            $new_comment = $this->get_all_comments($_GET['post_id']);
            echo print_r(json_encode($new_comment), true);
        }
    }

    public function get_all_comments($post_id){
        $loading = false;
        $comments = array();
        $id = $post_id;

        if (!$loading){
            $loading = true;

            $sql = "SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE comments.post_id=$id";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute();
            $i = 0;
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                $comments[$i] = $row;
                $comments[$i]['liked'] = $this->liked_or_not_comment($row['id'], $_SESSION['id']);
                $comments[$i]['like_count'] = $this->liked_or_not_comment($row['id'], $_SESSION['id'], 'counter')-1;
                $i++;
            }

            $loading = false;
            return $comments;
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

$newcom = new load_new_c();