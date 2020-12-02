<?php include('header.php'); ?>
<div class="grayed_bg">
    <div class="followers_followings" id="f_f_s">
        <div class="followers_followings_heading"><h4></h4><span>X</span></div>
    </div>
</div>
<!-- User Information -->
<section id="user" data-id="<?php echo $user_by_get['id']; ?>">
    <div class="container">
        <div class="row main-row">
            <div class="col-4 avatar-container">
                <?php if($user_by_get['avatar'] === null): ?>
                    <img src="./assets/user.png" alt="profile pic" class="avatar">
                <?php else: ?>
                    <img src="<?php echo BASE_URL; ?>/people/profile-pics/<?php echo $user_by_get['avatar']; ?>" alt="profile pic" class="avatar">
                <?php endif; ?>
            </div>
            <div class="col-8">
                <div class="profile-heading">
                    <div class="username-and-settings">
                        <h2 class="profile-author"><?php echo $user_by_get['username']; ?></h2>
                        <?php if($_SESSION['id'] === $user_by_get['id']): ?>
                            <img class="settings-img" src="./assets/settings.png" alt="settings">
                            <div class="settings-menu">
                                <ul>
                                    <li><a href="logout.php">Logout</a></li>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if($_SESSION['id'] === $user_by_get['id']): ?>
                        <a href="edit.php?d=general" class="edit-prof-btn">Edit profile</a>
                    <?php elseif($_SESSION['following_users'] === null || !reg_log::followed_or_not($_SESSION['following_users'], $user_by_get['id'])): ?>
                        <button class="follow-btn">Follow</button>
                    <?php else: ?>
                        <button class="message-btn">Message</button>
                        <button class="unfollow-btn"><img src="./assets/unfollow.png"></button>
                    <?php endif; ?>
                </div>
                <div class="profile-statistics">
                    <span><b><?php echo $user_by_get['post_count']; ?></b> posts</span>
                    <span class="followers_count"><b><?php echo $user_by_get['followers']; ?></b> followers</span>
                    <span class="following_count"><b><?php echo $user_by_get['following_users']; ?></b> following</span>
                </div>
                <div class="<?php if($_SESSION['id'] === $user_by_get['id']){echo 'profile-bio';}else{echo 'others-bio';} ?>">
                    <span class="your_name"><?php echo $user_by_get['name']; ?></span>
                    <span class="your_bio"><?php echo $user_by_get['bio']; ?></span>
                </div>
            </div>
        </div>
        <div class="user-tabs">
            <ul>
                <li class="active users-posts">
                    <svg version="1.1" id="user-posts-tab" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                         viewBox="0 0 670 670" style="enable-background:new 0 0 1366 768;" xml:space="preserve">
                        <style type="text/css">
                            .user-posts-grid-svg{fill:#262626;stroke-linecap:round;stroke-linejoin:round;}
                        </style>
                        <g>
                            <path class="user-posts-grid-svg" d="M626.8,19.7h-577c-16.6,0-30,13.4-30,30v577c0,16.6,13.4,30,30,30h577c16.6,0,30-13.4,30-30v-577
                            C656.8,33.2,643.3,19.7,626.8,19.7z M219.5,631.7H49.8c-2.8,0-5-2.2-5-5V497.9h174.8V631.7z M219.5,472.9H44.8V350.7h174.8V472.9z
                            M219.5,325.7H44.8V207.9h174.8V325.7z M219.5,182.9H44.8V49.7c0-2.8,2.2-5,5-5h169.8V182.9z M439.5,631.7h-195V497.9h195V631.7z
                            M439.5,472.9h-195V350.7h195V472.9z M439.5,325.7h-195V207.9h195V325.7z M439.5,182.9h-195V44.7h195V182.9z M631.8,626.7
                            c0,2.8-2.2,5-5,5H464.5V497.9h167.2V626.7z M631.8,472.9H464.5V350.7h167.2V472.9z M631.8,325.7H464.5V207.9h167.2V325.7z
                            M631.8,182.9H464.5V44.7h162.2c2.8,0,5,2.2,5,5V182.9z"/>
                        </g>
                        </svg>
                    <span>Posts</span>
                </li>
                <?php if($_SESSION['id'] === $user_by_get['id']): ?>
                    <li class="my-saves">
                        <svg version="1.1" id="user-saved-posts-tab" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                            viewBox="0 0 670 670" style="enable-background:new 0 0 667.4 669.6;" xml:space="preserve">
                            <style type="text/css">
                                .user-saved-posts-svg{fill:#262626;stroke-width: 10px;}
                            </style>
                            <path class="user-saved-posts-svg" d="M622.9,620.8L334.1,331.9L44.5,621.5V45.6h578.5V620.8z M33.5,34.6v597.9v2.6h13l11-11l276.6-276.6L610.6,624
                                l11,11H634v-3.3V34.6H33.5z"/>
                            </svg>
                        <span>Saved</span>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</section>

<!-- User Posts -->
<section id="user-posts">
    <div class="container">
        <?php if($_SESSION['id'] === $user_by_get['id']): ?>
        <div class="post-share"><button class="share-btn"><img src="./assets/upload.png"> SHARE</button></div>
        <?php endif; ?>
        <div class="row posts-row">
            <?php foreach($posts as $post){ ?>
                <?php $last_post = end($posts);
                if($post === $last_post){ ?>
                <div class="col-4 img-box" id="last_post">
                <div class="last_post_data" data-id="<?php echo $post['id'] ?>" style="display: none;"></div>
                <?php }else{ ?>
                    <div class="col-4 img-box">
                <?php }; ?>
                <a class="hover-infos" href="post.php?post=<?php echo $post['unique_id']; ?>">
                        <span>
                            <svg version="1.1" id="Layer_2_xA0_Image_1_" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                 x="0px" y="0px" viewBox="0 0 60 60" style="enable-background:new 0 0 60 60;" xml:space="preserve">
                            <style type="text/css">
                                .hover-heart{fill:#fafafa;stroke:none;stroke-width:0;stroke-linecap:round;stroke-linejoin:round;}
                            </style>
                            <g>
                                <g>
                                    <path class="hover-heart" d="M8.63,32.34L29.89,53.6l21.26-21.26c3.71-3.71,5.05-8.37,3.78-13.13c-1.46-5.45-6.1-10.09-11.55-11.55
                                        c-4.76-1.27-9.42,0.07-13.13,3.78l-0.35,0.35l-0.35-0.35c-2.64-2.64-5.82-4.01-9.19-4.01c-1.13,0-2.28,0.15-3.44,0.46
                                        C11.41,9.37,6.56,14.23,5.09,19.71C3.85,24.33,5.11,28.82,8.63,32.34z"/>
                                </g>
                                <g>
                                    <path class="hover-heart" d="M7.92,33.05l21.96,21.96l21.96-21.96c3.97-3.97,5.41-8.98,4.04-14.09C54.33,13.17,49.41,8.25,43.62,6.7
                                        c-4.96-1.33-9.83-0.02-13.74,3.69c-3.73-3.52-8.42-4.75-13.24-3.46C10.73,8.51,5.7,13.55,4.12,19.45
                                        C2.79,24.43,4.14,29.26,7.92,33.05z"/>
                                </g>
                            </g>
                            </svg>
                            <?php echo $post['post_likes']; ?>
                        </span>
                    <span>
                            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                 viewBox="0 0 300 300" style="enable-background:new 0 0 300 300;" xml:space="preserve">
                            <style type="text/css">
                                .st0{fill:#fafafa;}
                            </style>
                            <path class="st0" d="M267,269c-6.6-17.8-13.2-35.6-19.7-53.5c4.9-7.5,11.1-19,15.2-34.1c0,0,4.2-15.7,4.2-31.7
                                c0-66.2-53.8-120-120-120s-120,53.8-120,120c0,66.2,53.8,120,120,120c16.8,0,33.8-4.8,33.8-4.8c15.1-4.2,26.7-10.4,34.4-15.3
                                C232.3,256.1,249.6,262.6,267,269z"/>
                            </svg>
                            <?php echo $new_data->getInitialComments($post['id'], 'yes'); ?>
                        </span>
                </a>
                <img src="<?php echo BASE_URL; ?>/people/images/<?php echo $post['image']; ?>" alt="">
                </div>
            <?php } ?>
        </div>
    </div>
</section>

<?php include('footer.php'); ?>