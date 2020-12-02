<?php
include('./app/functions.php'); 
$middleware->usersOnly();    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aztagram</title>
    <!-- Font Awsome -->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css" integrity="sha512-1PKOgIY59xJ8Co8+NE6FZ+LOAZKjy+KY8iq0G4B3CyeY6wYHN3yt9PW0XpSriVlkMXe40PTKnXrLnZ9+fkDaog==" crossorigin="anonymous" /> -->
    <link rel="stylesheet" href="./assets/css/font-awsome/all.min.css">
    <!-- Main CSS -->
    <link rel="stylesheet" href="./assets/css/styles.css">
    <link rel="stylesheet" href="./assets/css/responsive.css">
</head>
<body>
    <!-- Header -->
    <header id="header">
        <div class="container">
            <div class="row">
                <div class="col-4 logo-area">
                    <a href="index.php"><img class="logo" src="./assets/logo.png" alt="Aztagram"></a>
                </div>
                <div class="col-4 search-container">
                    <form action="#" class="search-form" onsubmit="event.preventDefault()">
                        <input type="text" class="search-input">
                        <svg class="search-svg" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 300 300" style="enable-background:new 0 0 300 300;" xml:space="preserve">
                        <style type="text/css">
                            .search{fill:none;stroke:#a5a7aa;stroke-width:10;stroke-linecap:round;stroke-linejoin:round;}
                        </style>
                        <g id="Layer_1">
                        </g>
                        <g id="Layer_2">
                            <path class="search" d="M260.8,268.92c-25.76-27.72-51.51-55.43-77.27-83.15c-16.32,15.76-38.51,25.47-62.94,25.47
                                c-49.98,0-90.64-40.66-90.64-90.64s40.66-90.64,90.64-90.64s90.64,40.66,90.64,90.64c0,21.23-7.34,40.78-19.61,56.25
                                c26.08,28.07,52.16,56.13,78.24,84.2c-0.17,0.66,0,5.67-1.83,6.63S266.28,269.75,260.8,268.92z M199.24,120.6
                                c0-43.36-35.28-78.64-78.64-78.64c-43.36,0-78.64,35.28-78.64,78.64c0,43.36,35.28,78.64,78.64,78.64
                                C163.96,199.24,199.24,163.96,199.24,120.6z"/>
                        </g>
                        </svg>
                        <span class="search-span">Search</span>

                        <div class="search-results">
                            <span class="top-tick-result-bar"></span>
                            <div class="search-results-list"></div>
                        </div>
                    </form>
                </div>
                <div class="col-4 nav-pages">
                    <ul class="nav">
                        <li class="nav-item small-screen-search-icon">
                            <svg class="search-svg" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                viewBox="0 0 300 300" style="enable-background:new 0 0 300 300;" xml:space="preserve">
                            <style type="text/css">
                                .small-screen-search-svg{fill:#262626;stroke:#262626;stroke-width:10px;stroke-linecap:round;stroke-linejoin:round;}
                            </style>
                            <g id="Layer_1">
                            </g>
                            <g id="Layer_2">
                                <path class="small-screen-search-svg" d="M260.8,268.92c-25.76-27.72-51.51-55.43-77.27-83.15c-16.32,15.76-38.51,25.47-62.94,25.47
                                    c-49.98,0-90.64-40.66-90.64-90.64s40.66-90.64,90.64-90.64s90.64,40.66,90.64,90.64c0,21.23-7.34,40.78-19.61,56.25
                                    c26.08,28.07,52.16,56.13,78.24,84.2c-0.17,0.66,0,5.67-1.83,6.63S266.28,269.75,260.8,268.92z M199.24,120.6
                                    c0-43.36-35.28-78.64-78.64-78.64c-43.36,0-78.64,35.28-78.64,78.64c0,43.36,35.28,78.64,78.64,78.64
                                    C163.96,199.24,199.24,163.96,199.24,120.6z"/>
                            </g>
                            </svg>
                        </li>

                        <li class="nav-item <?php if($new_first_load->getCurrentPage() == 'index.php' || $new_first_load->getCurrentPage() == '') echo 'active'; ?>"><a href="index.php" class="nav-link">
                            <?xml version="1.0" encoding="utf-8"?>
                            <svg version="1.1" id="Layer_1_xA0_Image_1_" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                            x="0px" y="0px" viewBox="0 0 60 60" style="enable-background:new 0 0 60 60;" xml:space="preserve">
                            <style type="text/css">
                                .home{fill:none;stroke:#262626;stroke-width:3px;stroke-linecap:round;stroke-linejoin:round;}
                            </style>
                            <g>
                                <path class="home" d="M5.3,26.81v27.73h19.24V32.91c0-0.02,0-0.55,0.55-1.1c0.59-0.59,1.91-1.3,4.83-1.3c5.35,0,5.5,2.4,5.5,2.42
                                    v21.62H54.3V26.82L30,5.13L5.3,26.81z"/>
                            </g>
                            </svg>
                        </a></li>
                        <li class="nav-item direct-header-icon <?php if($new_first_load->getCurrentPage() == 'direct.php') echo 'active'; ?>"><a href="direct.php" class="nav-link">
                            <?php if($haveMsg > 0): ?>
                                <span class="msg_count"><?php echo $haveMsg ?></span>
                            <?php endif; ?>
                            <svg version="1.1" id="Layer_2_xA0_Image_1_" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                            x="0px" y="0px" viewBox="0 0 60 60" style="enable-background:new 0 0 60 60;" xml:space="preserve">
                            <style type="text/css">
                                .direct{fill:none;stroke:#262626;stroke-width:3px;stroke-linecap:round;stroke-linejoin:round;}
                            </style>
                            <path class="direct" d="M54.98,5.3L29.32,55.5l-6.58-29.07L4.72,5.3H54.98z M22.74,26.43c10.32-6.77,20.65-13.53,30.97-20.3"/>
                            </svg>
                        </a></li>
                        <li class="nav-item notifications_header">
                            <div class="nav-link notifications_header_link">
                                <?php if($ntfc > 0): ?>
                                <span class="ntf_count"><?php echo $ntfc ?></span>
                                <?php endif; ?>
                            <svg version="1.1" id="Layer_2_xA0_Image_1_" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                            x="0px" y="0px" viewBox="0 0 60 60" style="enable-background:new 0 0 60 60;" xml:space="preserve">
                            <style type="text/css">
                                .heart{fill:none;stroke:#262626;stroke-width:3px;stroke-linecap:round;stroke-linejoin:round;}
                            </style>
                                <path class="heart" d="M8.63,32.34L29.89,53.6l21.26-21.26c3.71-3.71,5.05-8.37,3.78-13.13c-1.46-5.45-6.1-10.09-11.55-11.55
                                    c-4.76-1.27-9.42,0.07-13.13,3.78l-0.35,0.35l-0.35-0.35c-2.64-2.64-5.82-4.01-9.19-4.01c-1.13,0-2.28,0.15-3.44,0.46
                                    C11.41,9.37,6.56,14.23,5.09,19.71C3.85,24.33,5.11,28.82,8.63,32.34z"/>
                            </svg>
                            </div>
                            <div class="notifications_container">
                                <span class="top-tick-notifications-bar"></span>
                                <div class="notifications-here"></div>
                            </div>
                        </li>
                        <li class="nav-item user-list-item <?php if($new_first_load->getCurrentPage() == 'user.php' && $_GET['user'] === $_SESSION['username']) echo 'active'; ?>" data-id="<?php echo $_SESSION['id']; ?>"><a href="user.php?user=<?php echo $_SESSION['username']; ?>" class="nav-link">
                            <?php if($_SESSION['avatar'] === null): ?>
                                <img class="user-header-pic" src="./assets/user-mini.png" alt="header avatar">
                            <?php else: ?>
                                <img src="<?php echo BASE_URL; ?>/people/profile-pics/<?php echo $_SESSION['avatar']; ?>" alt="header avatar" class="user-header-pic">
                            <?php endif; ?>
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </header>