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
    <div class="reg-container" id="register-page">
        <form method="POST">
            <div class="register-head">
                <img src="./assets/logo.png" alt="Aztagram">
                <p>Sign up to see photos and videos from your friends.</p>
            </div>
            <?php include(ROOT_PATH . '/helpers/messages.php'); ?>
            <div class="register-body">
                <input type="email" name="email" placeholder="Email" value="<?php echo $email; ?>">
                <input type="text" name="name" placeholder="Full Name" value="<?php echo $full_name; ?>">
                <input type="text" name="username" placeholder="Username" value="<?php echo $username; ?>">
                <input type="password" name="password" placeholder="Password">
                <input type="submit" class="sign-up-btn" name="sign-up" value="Sign Up" disabled>
                <div class="attention">By signing up, you agree to our <a href="#">Terms</a>, <a href="#">Data Policy</a> and <a href="#">Cookies Policy</a>.</div>
            </div>
        </form>
        <div class="have-acc">
            <div>Have an account? <a href="login.php">Log in</a></div>
        </div>
    </div>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==" crossorigin="anonymous"></script> -->
    <script src="./assets/js/jquery-3.4.1.min.js"></script>
    <script src="./assets/js/reg-log.js"></script>
</body>
</html>