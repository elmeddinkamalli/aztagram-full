<?php
require_once("C:/xampp/htdocs/aztagram/app/path.php");

require_once(ROOT_PATH.'/database/db.php');

//current page finder
require(ROOT_PATH.'/classes/first_load.php');

//middleware php
include(ROOT_PATH.'/classes/middleware.php');

//get initial data
include(ROOT_PATH.'/classes/get_data_from_db.php');

//include(ROOT_PATH.'/load_new_p.php');


//load classes for spesific pages
function loadClassFor(){
    global $new_data;
    global $posts;
    global $post;
    global $getSharedTime;
    global $errors, $email, $username, $full_name, $username_or_email;
    global $user_by_get;

    if(first_load::getCurrentPage() == 'index.php' ||
    first_load::getCurrentPage() == '' ||
    first_load::getCurrentPage() == 'user.php' ||
    first_load::getCurrentPage() == 'post.php' ||
    first_load::getCurrentPage() == 'direct.php' ||
    first_load::getCurrentPage() == 'edit.php'){
        include(ROOT_PATH.'/classes/header.php');
        $newHeader = new header();
        global $ntfc, $haveMsg;
        $ntfc = $newHeader->haveNotifications();
        $haveMsg = $newHeader->haveUnseenMessages();
    }
    
    if(first_load::getCurrentPage() == 'index.php' || first_load::getCurrentPage() == ''){
        $new_data = new get_data_from_db('posts', '');
        $posts = $new_data->get_posts();

        include(ROOT_PATH.'/classes/sharedTime.php');
        $getSharedTime = new sharedTime();
    }

    if(first_load::getCurrentPage() == 'register.php' ||
        first_load::getCurrentPage() == 'login.php' ||
        first_load::getCurrentPage() == 'user.php' ||
        first_load::getCurrentPage() == 'edit.php'){
        include(ROOT_PATH.'/classes/reg-log.php');
        $errors = $user_here->errors;
        $email = $user_here->email;
        $username = $user_here->username;
        $full_name = $user_here->full_name;
        $username_or_email = $user_here->username_or_email;
        $user_by_get = $user_here->user_by_get;
    }

    if(first_load::getCurrentPage() == 'user.php'){
        $new_data = new get_data_from_db('posts', ['user_id'=>$user_by_get['id']]);
        $posts = $new_data->get_posts();
        //print_r($posts);

        //include(ROOT_PATH.'/classes/sharedTime.php');
        //$getSharedTime = new sharedTime();
    }

    if(first_load::getCurrentPage() == 'post.php'){
        $new_data = new get_data_from_db('posts', ['unique_id'=>$_GET['post']], true);
        $post = $new_data->get_posts();
        //print_r($posts);

        include(ROOT_PATH.'/classes/sharedTime.php');
        $getSharedTime = new sharedTime();
    }

    if(first_load::getCurrentPage() == 'direct.php'){
        include(ROOT_PATH.'/classes/direct.php');
        include(ROOT_PATH.'/classes/sharedTime.php');
        $getSharedTime = new sharedTime();
        global $direct, $messages, $directeds;
        $direct = $directCode->direct;
        if(count($direct) > 0){
            $direct = $direct[0];
        }
        $messages = $directCode->messages;
        if(count($messages) > 0){
            $messages = json_decode($messages[0], true);
        }
        $directeds = $directCode->directeds;
        $directeds = $directeds[0];
    }
}

loadClassFor();

?>

<?php //echo json_encode($new_posts); ?>
