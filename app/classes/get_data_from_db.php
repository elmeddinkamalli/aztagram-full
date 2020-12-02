<?php
require_once("C:/xampp/htdocs/aztagram/app/path.php");
require_once(ROOT_PATH.'/database/db.php');

class get_data_from_db extends db
{
    private $table;
    private $singlePost;
    private $conditions = [];
    public $db = null;

    public function __construct($table, $conditions, $singlePost = false)
    {
        $this->table = $table;
        $this->conditions = $conditions;
        $this->singlePost = $singlePost;

        if(isset($_POST['searchText'])){
            $this->getBySearch($_POST['searchText']);
        }

        if (!isset($db->con)) return null;
        $this->db = $db;
    }

    public function executeQuery($sql, $data){
        $stmt = $this->connect()->prepare($sql);
        $values = array_values($data);
        $types = str_repeat('?', count($values));
        // $stmt->bindParam($types, ...$values);
        // $stmt->execute($values);
        $stmt->bindValue("?", $values, PDO::PARAM_INT);
        $stmt->execute($values);
        return $stmt;
    }

    public function get_posts(){
        $posts = array();
        $my_id = $_SESSION['id'];
        if (empty($this->conditions) && !$this->singlePost){
            $following_users = $_SESSION['following_users'];
            $sql = "SELECT $this->table.*, users.username, users.avatar FROM $this->table JOIN users ON $this->table.user_id = users.id";
            $sql .= " WHERE user_id in ($following_users, $my_id) ORDER BY posts.id DESC LIMIT 9";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute();
            while($records = $stmt->fetch(PDO::FETCH_ASSOC)){
                $i = 0;
                $records['liked'] = 'no';
                array_push($posts, $records);
            }
            foreach ($posts as &$post){
                $post['comments'] = $this->getInitialComments($post['id']);
                $post['liked'] = $this->liked_or_not($post['id'], $_SESSION['id']);
                $post['saved'] = $this->saved_or_not($post['id'], $_SESSION['id']);
            }
            return $posts;
        }else{
            if($this->singlePost){
                $sql = "SELECT $this->table.*, users.username, users.avatar FROM $this->table JOIN users ON $this->table.user_id = users.id";
            }else{
                $sql = "SELECT * FROM $this->table";
            }
            $i = 0;
            foreach ($this->conditions as $key => $value){
                if($i === 0){
                    $sql = $sql . ' WHERE ' . $key.'=?';
                }else{
                    $sql = $sql . ' AND ' . $key.'=?';
                }
                $i++;
            }
            if($this->singlePost){
                $sql .= " LIMIT 1";
                $stmt = $this->executeQuery($sql, $this->conditions);
                $records = $stmt->fetch(PDO::FETCH_ASSOC);
                $records['comments'] = $this->getInitialComments($records['id']);
                $records['liked'] = $this->liked_or_not($records['id'], $_SESSION['id']);
                $records['saved'] = $this->saved_or_not($records['id'], $_SESSION['id']);
                return $records;
            }else{
                $sql .= " ORDER BY posts.id DESC LIMIT 9";
                $stmt = $this->executeQuery($sql, $this->conditions);
                $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $records;
            }
        }
    }

    public function getInitialComments($post_id, $counter = 'no'){
        $count = 0;
        $comments = array();
        $sql_for_comments_count = "SELECT comments.comment, comments.id, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE post_id=$post_id ORDER BY comments.id DESC";
        $stmt_for_comments_count = $this->connect()->prepare($sql_for_comments_count);
        $stmt_for_comments_count->execute();
        while($row_comments = $stmt_for_comments_count->fetch(PDO::FETCH_ASSOC)){
            $count++;
            $comments['comment-'.$count] = $row_comments;
            $comments['comment-'.$count]['liked'] = $this->liked_or_not_comment($row_comments['id'], $_SESSION['id']);
            $comments['comment-'.$count]['like_count'] = $this->liked_or_not_comment($row_comments['id'], $_SESSION['id'], 'counter')-1;
        }
        $comments['comment_count'] = $count;
        if($counter === 'yes'){
            return $count;
        }
        return $comments;
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

    public function getBySearch($searchText){
        $users = array();

        $first_class_query = "SELECT id, username, name, avatar FROM users WHERE username LIKE '%$searchText%'";
        $first_class_stmt = $this->connect()->prepare($first_class_query);
        $first_class_stmt->execute();
        $first_class_record = $first_class_stmt->fetchAll();
        array_push($users, $first_class_record);
        $users = $users[0];

        $second_class_query = "SELECT id, username, name, avatar FROM users WHERE name LIKE '%$searchText%'";
        $second_class_stmt = $this->connect()->prepare($second_class_query);
        $second_class_stmt->execute();
        $second_class_record = $second_class_stmt->fetchAll();
        if($second_class_record){
            $i = 0;
            foreach ($second_class_record as $user){
                if(!in_array($user, $users)){
                    array_push($users, $user);
                }
            }
        }

        print_r(json_encode($users));
    }

}

if(isset($_POST['searchText'])){
    $newData = new get_data_from_db('users', $_POST['searchText']);
}