<?php

class middleware{
    public function usersOnly($r = 'login.php'){
        if(empty($_SESSION['id'])){
            $_SESSION['message'] = "You need to login first.";
            $_SESSION['type'] = "error";
            header('location: ' . BASE_URL . '/' . $r);
            exit();
        }
    }

    public function guestsOnly($r = 'index.php'){
        if(isset($_SESSION['id'])){
            header('location: ' . BASE_URL . '/' . $r);
            exit();
        }
    }
}

$middleware = new middleware();