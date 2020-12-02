<?php

session_start();

unset($_SESSION['id']);
unset($_SESSION['username']);
unset($_SESSION['message']); 
unset($_SESSION['type']);
session_destroy();

header('location: index.php');