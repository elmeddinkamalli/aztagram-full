<?php

require_once("C:/xampp/htdocs/aztagram/app/path.php");
require_once(ROOT_PATH.'/database/db.php');

class direct extends db
{
    public $loading = false;
    public $directeds = array();
    public $direct = array();
    public $messages = array();

    public function __construct()
    {
        $this->getDirecteds($_SESSION['id']);

        if (isset($_POST['for'])){
            if($_POST['for'] === 'startNewDirect'){
                $this->startNewDirect($_POST['partner_id']);
            }else if($_POST['for'] === 'changeDirect'){
                $this->changeDirect($_POST['partner_id']);
            }else if($_POST['for'] === 'liveDirecteds'){
                $this->getLiveDataofDirecteds();
            }else if($_POST['for'] === 'liveChat'){
                $this->getLiveChatData($_POST['partner_id'], $_POST['last_msg']);
            }else if($_POST['for'] === 'makeSeen'){
                $this->makeMessageSeen($_POST['partner_id']);
            }
        }

        if(isset($_GET['directed'])){
            $this->chechAndStart($_GET['directed']);
        }

        if(isset($_POST['message'])){
            $this->sendMessage($_POST['message'], $_POST['direct_id']);
        }
    }

    private function startNewDirect($partner_id){
        $my_id = $_SESSION['id'];
        $ids = $my_id.','.$partner_id;

        $query = "SELECT id FROM direct WHERE partner_1 in ($ids) AND partner_2 in ($ids)";
        $stmt = $this->connect()->prepare($query);
        $stmt->execute();
        $record = $stmt->fetch();

        if(!$record){
            $query = "INSERT INTO direct SET partner_1=$my_id, partner_2=$partner_id, unique_id=CONCAT(SUBSTRING('ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789', RAND()*34+1, 1),
                SUBSTRING('ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789', RAND()*34+1, 1),
                SUBSTRING('ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789', RAND()*34+1, 1),
                SUBSTRING('ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789', RAND()*34+1, 1),
                SUBSTRING('ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789', RAND()*34+1, 1),
                SUBSTRING('ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789', RAND()*34+1, 1),
                SUBSTRING('ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789', RAND()*34+1, 1),
                SUBSTRING('ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789', RAND()*34+1, 1)
                )";
            $stmt = $this->connect()->prepare($query);
            $stmt->execute();
        }

        $query = "SELECT unique_id FROM direct WHERE partner_1 in ($ids) AND partner_2 in ($ids)";
        $stmt = $this->connect()->prepare($query);
        $stmt->execute();
        $record = $stmt->fetch();
        echo $record['unique_id'];
    }

    private function chechAndStart($unique_id){
        $query = "SELECT * FROM direct WHERE unique_id = ?";
        $stmt = $this->connect()->prepare($query);
        $stmt->execute(array($unique_id));
        $record = $stmt->fetch();

        if(!$record){
            header('Location: direct.php');
        }else{
            if($record['partner_1'] == $_SESSION['id']){
                $partner_id = $record['partner_2'];
            }else{
                $partner_id = $record['partner_1'];
            }

            $query = "SELECT username, avatar FROM users WHERE id=$partner_id";
            $stmt = $this->connect()->prepare($query);
            $stmt->execute();
            $record_u = $stmt->fetch();
            $record['username'] = $record_u['username'];
            $record['avatar'] = $record_u['avatar'];

            $query = "UPDATE direct SET not_seen = 0 WHERE unique_id = '$unique_id'";
            $stmt = $this->connect()->prepare($query);
            $stmt->execute();

            $messages = $record['messages'];
            array_push($this->messages, $messages);
            //unset($record['messages']);
            array_push($this->direct, $record);
        }
    }

    private function getDirecteds($my_id){
        $query = "SELECT direct.*, users.id, users.username, users.avatar FROM direct JOIN users ON users.id in (direct.partner_1, direct.partner_2) WHERE partner_1 = $my_id OR partner_2 = $my_id ORDER BY last_update DESC";
        $stmt = $this->connect()->prepare($query);
        $stmt->execute();
        $records = $stmt->fetchAll();
        foreach ($records as $key => $r){
            $msgs = json_decode($records[$key]['messages'], true);
            if(count($msgs) > 0){
                $last_msg = $msgs[count($msgs)-1];
                $records[$key]['last_message'] = substr($last_msg['msg'], 0, 19).'...';
                unset($records[$key]['messages']);
            }else{
                $records[$key]['last_message'] = null;
                unset($records[$key]['messages']);
            }

            if($records[$key]['id'] == $my_id){
                unset($records[$key]);
            }
        }


        array_push($this->directeds, $records);
    }

    private function sendMessage($sent_message, $direct_id){
        if(!$this->loading){
            $this->loading = true;

            $check_query = "SELECT * FROM direct WHERE unique_id='$direct_id'";
            $check_stmt = $this->connect()->prepare($check_query);
            $check_stmt->execute();
            $record = $check_stmt->fetch();
            if($record['partner_1'] === $_SESSION['id']){
                $partner_id = $record['partner_2'];
            }else{
                $partner_id = $record['partner_1'];
            }

            $messages = json_decode($record['messages'], true);

            $message = array();
            $message['from'] = $_SESSION['id'];
            $message['msg'] = htmlentities($sent_message);
            array_push($messages, $message);
            $messages = json_encode($messages);

            date_default_timezone_set('Asia/Baku');
            $last_update = date('Y-m-d H:i:s', time());
            $query = "UPDATE direct SET not_seen_id = $partner_id, messages = ?, last_update = '$last_update' WHERE unique_id = '$direct_id'";
            $stmt = $this->connect()->prepare($query);
            $stmt->execute(array($messages));

            echo "sent";
        }
        $this->loading = false;
    }

    private function changeDirect($partner_id){
        $my_id = $_SESSION['id'];
        $query = "SELECT direct.*, users.username, users.avatar FROM direct JOIN users ON users.id=$partner_id WHERE partner_1 in ($my_id, $partner_id) AND partner_2 in ($my_id, $partner_id)";
        $stmt = $this->connect()->prepare($query);
        $stmt->execute();
        $record = $stmt->fetchAll();
        $record = $record[0];
        print_r(json_encode($record, true));

        $unique_id = $record['unique_id'];
        $query = "UPDATE direct SET not_seen_id = 0 WHERE unique_id = '$unique_id'";
        $stmt = $this->connect()->prepare($query);
        $stmt->execute();
    }

    private function getLiveDataofDirecteds(){
        $liveData = array();

        $my_id = $_SESSION['id'];
        $query = "SELECT not_seen_id, partner_1, partner_2, messages FROM direct WHERE partner_1 = $my_id OR partner_2 = $my_id";
        $stmt = $this->connect()->prepare($query);
        $stmt->execute();
        $records = $stmt->fetchAll();

        if($records){
            foreach ($records as $record){
                $data = array();
                if($record['not_seen_id'] === $my_id){
                    if($record['partner_1'] === $my_id){
                        $data['id'] = $record['partner_2'];
                    }else{
                        $data['id'] = $record['partner_1'];
                    }

                    $msgs = json_decode($record['messages'], true);
                    $last_msg = $msgs[count($msgs)-1];
                    $data['last_message'] = substr($last_msg['msg'], 0, 19).'...';
                    array_push($liveData, $data);
                }
            }
        }

        print_r(json_encode($liveData, true));
    }

    private function getLiveChatData($partner_id, $last_msg){
        $my_id = $_SESSION['id'];
        $query = "SELECT messages FROM direct WHERE partner_1 in ($my_id, $partner_id) AND partner_2 in ($my_id, $partner_id)";
        $stmt = $this->connect()->prepare($query);
        $stmt->execute();
        $record = $stmt->fetch();

        $messages = json_decode($record['messages'], true);

        $messages = array_slice($messages, (int)$last_msg+1, count($messages)-1);

        $query = "UPDATE direct SET not_seen=0 WHERE partner_1 in ($my_id, $partner_id) AND partner_2 in ($my_id, $partner_id)";
        $stmt = $this->connect()->prepare($query);
        $stmt->execute();

        print_r(json_encode($messages));
    }

    private function makeMessageSeen($partner_id){
        $my_id = $_SESSION['id'];
        $query = "UPDATE direct SET not_seen_id = 0 WHERE partner_1 in ($my_id, $partner_id) AND partner_2 in ($my_id, $partner_id)";
        $stmt = $this->connect()->prepare($query);
        $stmt->execute();
    }
}

$directCode = new direct();