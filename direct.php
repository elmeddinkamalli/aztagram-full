<?php include('header.php'); ?>

<div class="grayed_bg">
    <div class="followers_followings direct-new-message-container" id="f_f_s">
        <div class="followers_followings_heading"><h4></h4><span>X</span></div>
    </div>
</div>
<!-- Directs -->
<section id="directs">
    <div class="container">
        <div class="row">
            <div class="col to-direct">
                <div class="direct-heading">
                    <h5>Direct</h5>
                    <img class="send-new-message" src="./assets/message.png" alt="direct">
                </div>
                <div class="directs">
                    <?php if($directeds): ?>
                    <?php foreach ($directeds as $d): ?>
                    <div class="d-person <?php echo $d['not_seen_id'] === $_SESSION['id'] ? 'not_seen' : '' ?>" data-id="<?php echo $d['id']; ?>">
                        <div class="d-person-img">
                            <?php if($d['avatar'] === null): ?>
                            <img src="./assets/user-mini.png" alt="user">
                            <?php else: ?>
                            <img src="./people/profile-pics/<?php echo $d['avatar']; ?>" alt="user">
                            <?php endif; ?>
                        </div>
                        <div class="d-person-info">
                            <h4><?php echo $d['username']; ?></h4>
                            <?php if($d['last_message'] !== null): ?>
                                <p class="last_msg"><?php echo $d['last_message']; ?></p><span class="last_msg_time"><?php echo $getSharedTime->get_the_different($d['last_update'], true); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col selected-direct">
                <?php if($direct): ?>
                    <div class="direct-heading_m" data-id="<?php echo $_SESSION['id']===$direct['partner_1'] ? $direct['partner_2'] : $direct['partner_1'] ?>">
                        <a class="direct-heading-user-avatar-link" href="user.php?user=<?php echo $direct['username']; ?>">
                            <?php if($direct['avatar'] === null): ?>
                            <img class="direct-selected-user-avatar" src="./assets/user-mini.png">
                            <?php else: ?>
                            <img class="direct-selected-user-avatar" src="./people/profile-pics/<?php echo $direct['avatar']; ?>">
                            <?php endif; ?>
                        </a>
                        <a class="direct-heading-username" href="user.php?user=<?php echo $direct['username']; ?>"><?php echo $direct['username']; ?></a>
                    </div>
                    <div class="messages-container">
                        <ul class="messages-ul">
                            <?php if(count($messages) > 0): ?>
                            <?php foreach ($messages as $key => $message): ?>
                                <?php if($messages[$key]['from'] === $_SESSION['id']): ?>
                                    <li class="my_msg"><span class="msg_span_el" data-last-msg="<?php echo $key; ?>"><?php echo $messages[$key]['msg']; ?></span></li>
                                <?php else: ?>
                                    <li class="part_msg"><span class="msg_span_el" data-last-msg="<?php echo $key; ?>"><?php echo $messages[$key]['msg']; ?></span></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <form class="direct-form" action="direct.php">
                        <input class="message-input" type="text" placeholder="Message..." data-direct-id="<?php echo $direct['unique_id'] ?>">
                        <input class="message-send-input" type="submit" value="Send">
                    </form>
                <?php else: ?>
                <div class="empty-selected-direct">
                    <img src="./assets/empty-direct.png" alt="empty direct">
                    <h2>Your Messages</h2>
                    <p>Send private photos and messages to a friend or group.</p>
                    <button class="send-new-message">Send Message</button>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include('footer.php'); ?>
