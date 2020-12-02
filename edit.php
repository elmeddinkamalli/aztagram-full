<?php include('header.php'); ?>

<section id="edit-page">
    <div class="container">
        <div class="row edit-page-row">
            <div class="col edit-page-left-col">
                <ul class="edit-left-ul">
                    <li class="edit-list-op edit-profile <?php echo $_GET['d'] === 'general' ? 'active' : '' ?>">Edit Profile</li>
                    <li class="edit-list-op edit-password <?php echo $_GET['d'] === 'password' ? 'active' : '' ?>">Change Password</li>
                </ul>
            </div>
            <div class="col edit-page-right-col <?php echo $_GET['d'] === 'general' ? 'active' : '' ?>">
                    <div class="change_avatar">
                        <input class="change-avatar-file-inp" type="file" style="display: none;">
                        <img src="<?php if($user_by_get['avatar'] == null){ ?>./assets/user-mini.png<?php }else{ ?> ./people/profile-pics/<?php echo $user_by_get['avatar']; } ?>">
                        <div class="edit-username-change-av-btn"><?php echo $user_by_get['username']; ?><span class="change-avatar">Change Profile Photo</span></div>
                    </div>
                <form class="edit-form" action="edit.php?d=general" method="post">

                    <div class="edit-part-all">
                        <div class="edit-part-left">
                            <span>Name</span>
                        </div>
                        <div class="edit-part-right">
                            <input type="text" name="name" value="<?php echo $user_by_get['name']; ?>">
                            <p>Help people discover your account by using the name you're known by: either your full name, nickname, or business name.</p>
                        </div>
                    </div>

                    <div class="edit-part-all">
                        <div class="edit-part-left">
                            <span>Username</span>
                        </div>
                        <div class="edit-part-right">
                            <input type="text" name="username" value="<?php echo $user_by_get['username']; ?>">
                        </div>
                    </div>

                    <div class="edit-part-all">
                        <div class="edit-part-left">
                            <span>Bio</span>
                        </div>
                        <div class="edit-part-right">
                            <textarea name="bio"><?php echo $user_by_get['bio']; ?></textarea>
                        </div>
                    </div>

                    <div class="edit-part-all">
                        <div class="edit-part-left">
                            <span>Email</span>
                        </div>
                        <div class="edit-part-right">
                            <input type="email" name="email" value="<?php echo $user_by_get['email']; ?>">
                        </div>
                    </div>

                    <div class="edit-part-all">
                        <div class="edit-part-left">
                            <span>Gender</span>
                        </div>
                        <div class="edit-part-right">
                            <select name="gender" id="gender">
                                <option value="Prefer Not To Say" <?php echo $user_by_get['gender'] === 'Prefer Not To Say' ? 'selected' : '' ?>>Prefer Not To Say</option>
                                <option value="Male" <?php echo $user_by_get['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                                <option value="Female" <?php echo $user_by_get['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
                            </select>
                        </div>
                    </div>

                    <?php include(ROOT_PATH . '/helpers/messages.php'); ?>

                    <div>
                        <div>
                            <input class="edit-submit-btn" name="update_user_btn" type="submit" value="Submit">
                        </div>
                    </div>
                </form>
            </div>

            <div class="col edit-page-pass-right-col <?php echo $_GET['d'] === 'password' ? 'active' : '' ?>">
                <div class="edit-page-pass-left">
                    <img class="edit-page-pass-avatar" src="<?php if($user_by_get['avatar'] == null){ ?>./assets/user-mini.png<?php }else{ ?> ./people/profile-pics/<?php echo $user_by_get['avatar']; } ?>">
                    <div>Old Password</div>
                    <div>New Password</div>
                    <div>Confirm New Password</div>
                </div>
                <form action="edit.php?d=password" method="post">
                    <span><?php echo $user_by_get['username']; ?></span>
                    <input type="password" name="old_pass">
                    <input type="password" name="new_pass">
                    <input type="password" name="confirm_new_pass">
                    <?php include(ROOT_PATH . '/helpers/messages.php'); ?>
                    <input class="edit-submit-btn" type="submit" value="Change Password" name="update_password">
                </form>
            </div>
        </div>
    </div>
</section>

<?php include('footer.php'); ?>