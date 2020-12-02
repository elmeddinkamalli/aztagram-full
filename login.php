<?php
include('./app/functions.php'); 
$middleware->guestsOnly();    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Aztagram</title>
    <!-- Font Awsome -->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css" integrity="sha512-1PKOgIY59xJ8Co8+NE6FZ+LOAZKjy+KY8iq0G4B3CyeY6wYHN3yt9PW0XpSriVlkMXe40PTKnXrLnZ9+fkDaog==" crossorigin="anonymous" /> -->
    <link rel="stylesheet" href="./assets/css/font-awsome/all.min.css">
    <!-- Main CSS -->
    <link rel="stylesheet" href="./assets/css/styles.css">
    <link rel="stylesheet" href="./assets/css/responsive.css">
</head>
<body>
    <div class="reg-container" id="login-page">
        <form method="POST">
            <div class="register-head">
                <img src="./assets/logo.png" alt="Aztagram">
            </div>
            <?php include(ROOT_PATH . '/helpers/messages.php'); ?>
            <div class="register-body">
                <input type="text" name="username_or_email" placeholder="Username or email" value="<?php echo $username_or_email; ?>">
                <input type="password" name="password" placeholder="Password">
                <input type="submit" class="log-in-btn" name="log-in" value="Log In" disabled>
                <div class="attention"><a href="#">Forgot password?</a></div>
            </div>
        </form>
        <div class="have-acc">
            <div>Don't have an account? <a href="register.php">Sign up</a></div>
        </div>
    </div>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==" crossorigin="anonymous"></script> -->
    <script src="./assets/js/jquery-3.4.1.min.js"></script>
    <script src="./assets/js/reg-log.js"></script>
</body>
</html>