<?php include('header.php'); ?>

<!-- Main section -->
<section id="posts">
    <div class="container">
        <div class="row">
            <?php foreach ($posts as $post){ ?>
            <!-- Post -->
                <?php $last_post = end($posts);
                    if($post === $last_post){ ?>
                        <div class="col post" id="last_post">
                        <div class="last_post_data" data-id="<?php echo $post['id'] ?>" style="display: none;"></div>
                <?php }else{ ?>
                        <div class="col post">
                <?php }; ?>
                <!-- Post Heading -->
                <div class="post-heading">
                    <a href="./user.php?user=<?php echo $post['username']; ?>" class="post-heading-link">
                        <?php if($post['avatar'] === null): ?>
                            <img class="post-user-img" src="<?php echo BASE_URL ?>/assets/user-mini.png" alt="user">
                        <?php else: ?>
                            <img class="post-user-img" src="<?php echo BASE_URL ?>/people/profile-pics/<?php echo $post['avatar']; ?>" alt="user">
                        <?php endif; ?>
                        <span><?php echo $post['username']; ?></span>
                    </a>
                    <div class="post-settings-points"><span></span><span></span><span></span></div>
                </div>

                <!-- Post Image -->
                <div class="post-img">
                    <img src="<?php echo BASE_URL ?>/people/images/<?php echo $post['image']; ?>" alt="post img">
                </div>

                <!-- Post Actions -->
                <div class="post-actions" data-id="<?php echo $post['id']; ?>">

                    <svg version="1.1" id="post-heart-svg" class="<?php echo $post['liked']; ?> like" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                         x="0px" y="0px" viewBox="0 0 60 60" style="enable-background:new 0 0 60 60;" xml:space="preserve">
                        <style type="text/css">
                            .post-heart{fill:none;stroke:#262626;stroke-width:3px;stroke-linecap:round;stroke-linejoin:round;}
                        </style>
                        <path class="post-heart" d="M8.63,32.34L29.89,53.6l21.26-21.26c3.71-3.71,5.05-8.37,3.78-13.13c-1.46-5.45-6.1-10.09-11.55-11.55
                            c-4.76-1.27-9.42,0.07-13.13,3.78l-0.35,0.35l-0.35-0.35c-2.64-2.64-5.82-4.01-9.19-4.01c-1.13,0-2.28,0.15-3.44,0.46
                            C11.41,9.37,6.56,14.23,5.09,19.71C3.85,24.33,5.11,28.82,8.63,32.34z"/>
                        </svg>

                    <svg version="1.1" id="post-comment-svg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                         viewBox="0 0 300 300" style="enable-background:new 0 0 300 300;" xml:space="preserve">
                        <style type="text/css">
                            .post-comment{fill:none;stroke:#262626;stroke-width:15px;stroke-linecap:round;stroke-linejoin:round;}
                        </style>
                        <path class="post-comment" d="M270.23,270.14l-59.7-20.89c-19.58,12.61-42.87,19.92-67.84,19.92c-69.33,0-125.74-56.4-125.74-125.74
                            C16.95,74.1,73.36,17.7,142.69,17.7s125.73,56.4,125.73,125.73c0,23.92-6.71,46.29-18.34,65.35L270.23,270.14z M243.26,209.54
                            c4.87-7.52,11.14-19.02,15.24-34.12c0,0,4.23-15.67,4.23-31.66c0-66.17-53.83-120.01-120.01-120.01S22.71,77.59,22.71,143.76
                            c0,66.18,53.84,120.02,120.02,120.02c16.78,0,33.75-4.83,33.75-4.83c15.07-4.22,26.68-10.39,34.44-15.27
                            c17.36,6.44,34.72,12.88,52.08,19.32C256.42,245.18,249.84,227.36,243.26,209.54z"/>
                        </svg>

                    <svg version="1.1" id="post-direct-svg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                         x="0px" y="0px" viewBox="0 0 60 60" style="enable-background:new 0 0 60 60;" xml:space="preserve">
                        <style type="text/css">
                            .post-direct{fill:none;stroke:#262626;stroke-width:3px;stroke-linecap:round;stroke-linejoin:round;}
                        </style>
                        <path class="post-direct" d="M54.98,5.3L29.32,55.5l-6.58-29.07L4.72,5.3H54.98z M22.74,26.43c10.32-6.77,20.65-13.53,30.97-20.3"/>
                        </svg>

                    <svg version="1.1" id="post-save-svg" class="<?php echo $post['saved']; ?> saved" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                            viewBox="0 0 594.8 669.6" style="enable-background:new 0 0 594.8 669.6;" xml:space="preserve">
                        <style type="text/css">
                            .post-save{fill:none;stroke:#262626;stroke-width:34px;stroke-miterlimit:10;}
                        </style>
                        <polygon class="post-save" points="33.5,31.6 33.5,637.5 299.3,330.4 564.6,637.5 564.6,31.6 "/>
                        </svg>

                </div>

                <!-- Post infos -->
                <div class="post-info">
                    <div class="post-likes"><span><?php echo $post['post_likes']; ?></span> likes</div>
                    <div class="post-author-title"><a href="user.php?user=<?php echo $post['username']; ?>" class="post-author"><?php echo $post['username']; ?></a><span class="post-title"> <?php echo $post['description']; ?></span></div>
                </div>

                <!-- Post comments -->
                <div class="post-comments" id="<?php echo mt_rand(100000,999999); ?>">
                    <?php if ($post['comments']['comment_count'] > 2){ ?>
                    <div class="post-comments-view-more" data-id-com="<?php echo $post['id']; ?>">View all <span><?php echo $post['comments']['comment_count'] ?></span> comments</div>
                    <?php } ?>
                    <div class="post-selected-comments">
                        <?php if(array_key_exists('comment-2', $post['comments'])){ ?>
                        <div class="post-comment">
                            <a href="user.php?user=<?php echo $post['comments']['comment-2']['username'] ?>"><span class="post-comment-author"><?php echo $post['comments']['comment-2']['username'] ?></span></a>
                            <span class="post-raw-comment"> <?php echo $post['comments']['comment-2']['comment'] ?></span>
                            <div class="comment_like_box">
                            <input type="hidden" class="comment-id" data-id="<?php echo $post['comments']['comment-2']['id']; ?>">
                            <?php if($post['comments']['comment-2']['like_count'] !== 0) { ?>
                            <span class="comment_like_count"><?php echo $post['comments']['comment-2']['like_count']; ?></span>
                            <?php }; ?>
                            <svg version="1.1" id="post-comment-svg-heart" class="like_this_comment <?php if($post['comments']['comment-2']['liked'] === "yes"){echo "liked";}else{echo "not-liked";} ?>" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                 x="0px" y="0px" viewBox="0 0 60 60" style="enable-background:new 0 0 60 60;" xml:space="preserve">
                                <style type="text/css">
                            .post-comment-heart{stroke:#000000;stroke-linecap:round;stroke-linejoin:round;}
                                </style>
                                <path class="post-comment-heart" d="M8.63,32.34L29.89,53.6l21.26-21.26c3.71-3.71,5.05-8.37,3.78-13.13c-1.46-5.45-6.1-10.09-11.55-11.55
                                    c-4.76-1.27-9.42,0.07-13.13,3.78l-0.35,0.35l-0.35-0.35c-2.64-2.64-5.82-4.01-9.19-4.01c-1.13,0-2.28,0.15-3.44,0.46
                                    C11.41,9.37,6.56,14.23,5.09,19.71C3.85,24.33,5.11,28.82,8.63,32.34z"/>
                                </svg>
                        </div>
                        </div>
                        <?php } ?>
                            <?php if(array_key_exists('comment-1', $post['comments'])){ ?>
                                <div class="post-comment">
                                    <a href="user.php?user=<?php echo $post['comments']['comment-1']['username'] ?>"><span class="post-comment-author"><?php echo $post['comments']['comment-1']['username'] ?></span></a>
                                    <span class="post-raw-comment"> <?php echo $post['comments']['comment-1']['comment'] ?></span>
                                    <div class="comment_like_box">
                                    <input type="hidden" class="comment-id" data-id="<?php echo $post['comments']['comment-1']['id']; ?>">
                                    <?php if($post['comments']['comment-1']['like_count'] !== 0) { ?>
                                    <span class="comment_like_count"><?php echo $post['comments']['comment-1']['like_count']; ?></span>
                                    <?php }; ?>
                                    <svg version="1.1" id="post-comment-svg-heart" class="like_this_comment <?php if($post['comments']['comment-1']['liked'] === "yes"){echo "liked";}else{echo "not-liked";} ?>" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                         x="0px" y="0px" viewBox="0 0 60 60" style="enable-background:new 0 0 60 60;" xml:space="preserve">
                                <style type="text/css">
                                    .post-comment-heart{stroke:#000000;stroke-linecap:round;stroke-linejoin:round;}
                                </style>
                                    <path class="post-comment-heart" d="M8.63,32.34L29.89,53.6l21.26-21.26c3.71-3.71,5.05-8.37,3.78-13.13c-1.46-5.45-6.1-10.09-11.55-11.55
                                c-4.76-1.27-9.42,0.07-13.13,3.78l-0.35,0.35l-0.35-0.35c-2.64-2.64-5.82-4.01-9.19-4.01c-1.13,0-2.28,0.15-3.44,0.46
                                C11.41,9.37,6.56,14.23,5.09,19.71C3.85,24.33,5.11,28.82,8.63,32.34z"/>
                                </svg>
                                </div>
                                </div>
                            <?php } ?>
                    </div>

                    <div class="post-timeline"><?php echo $getSharedTime->get_the_different($post['created_at']); ?></div>

                </div>

                <!-- Post comment form -->
                <div class="post-comment-form-container">
                    <form action="#" method="post">
                        <input type="hidden" class="comment-input" id="<?php echo $post['id']; ?>">
                        <textarea class="comment-textarea" name="comment" placeholder="Add a comment..."></textarea>
                        <input class="comment-submit" type="submit" value="Post">
                    </form>
                </div>

            </div>
            <?php }; ?>
        </div>
    </div>
    <div class="grayed_bg">
        <div class="show-likes">
            <div class="show-likes-heading"><h4></h4><span>X</span></div>
            <ul></ul>
        </div>
    </div>
</section>

<?php include('footer.php'); ?>
