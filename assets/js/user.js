//User page

class userPageScripts{
    sent = false;
    constructor(){
        if($('.your_bio').length){
            this.yourBio = $('.your_bio');
            this.mainRow = $('.main-row');
            this.specialEvents();
        }
        this.increaseHeight();
        userPageScripts.fitImgSize();
    }

    //Events
    specialEvents(){
        $(window).resize(this.increaseHeight.bind(this));
        $(window).resize(userPageScripts.fitImgSize.bind(this));
        $('.settings-img').on("click", this.openMenu);
        $(document).on("click", '.unfollow-btn', this.unfollowRequest.bind(this));
        $(document).on("click", '.follow-btn', this.followRequest.bind(this));
        $(document).on("click", '.my-saves', this.showSaves.bind(this));
        $(document).on("click", '.users-posts', this.showPosts.bind(this));
        $(document).on("click", '.followers_count', this.showFollowers.bind(this));
        $(document).on("click", '.following_count', this.showFollowings.bind(this));
        $(document).on("click", '.grayed_bg', this.closeFF.bind(this));
        $(document).on("click", '.share-btn', this.loadShareScreen.bind(this));
        $(document).on("click", '.empty-preview', ()=>{$('.share__select-image').click()});
        $(document).on("change", '.share__select-image', this.showPreview.bind(this));
        $(document).on("submit", '.share-form', this.shareNewPost.bind(this));
    }

    //Methods
    increaseHeight(){
       if(window.innerWidth < 735){
            var bioHeight = 145 + this.yourBio.height() + 10;
            this.mainRow.css('height', bioHeight);
       }else{
            this.mainRow.css('height', '');
        }
    }

    static fitImgSize(){
        if($('.users-posts').is('.active')){
            var imgBox = $('.posts-row .img-box');
            var boxWidth = imgBox.width();
            return imgBox.css("height", boxWidth);
        }
        if($('.my-saves').is('.active')){
            var imgBox = $('.saves-row .img-box');
            var boxWidth = imgBox.width();
            return imgBox.css("height", boxWidth);
        }
        
    }

    openMenu(){
        if($('.settings-menu').is('.active')){
            $('.settings-menu').removeClass('active');
        }else{
            $('.settings-menu').addClass('active');
        }
    }

    generateButtons_u(d,e){
        if(d === 'f'){
            $('.follow-btn').remove();
            var button = $("<button>", {"class": "unfollow-btn", "value": "Unfollow"});
            button.append(`<img src="./assets/unfollow.png">`);
            var msg_button = $("<button>", {"class": "message-btn", "value": "Message"});
            msg_button.html('Message');
            $('.profile-heading').append(msg_button);
            $('.profile-heading').append(button);
        }else if(d === 'uf'){
            $('.unfollow-btn').remove();
            $('.message-btn').remove();
            var button = $("<button>", {"class": "follow-btn", "value": "Follow"});
            button.html("Follow");
            $('.profile-heading').append(button);
        }else if(d === 'quick-list-f'){
            var person_element = e.parent();
            e.remove();
            var button = $("<button>", {"class": "unfollow-btn quick-list", "value": "Unfollow"});
            button.html("Unfollow");
            person_element.append(button);
        }else if(d === 'quick-list-uf'){
            var person_element = e.parent();
            e.remove();
            var button = $("<button>", {"class": "follow-btn quick-list", "value": "Follow"});
            button.html("Follow");
            person_element.append(button);
        }
    }

    followRequest(e, _this = this){
        e.preventDefault();
        if($(e.target).is('.quick-list')){
            var user_id = $(e.target).parent().data('id');
        }else{
            var user_id = $('#user').data('id');
        }
        var my_id = $('.user-list-item').data('id');
        if(!_this.sent){
            _this.sent = true;
            $.ajax({
                type: 'POST',
                url: './app/classes/reg-log.php',
                data: {my_id:my_id, user_id:user_id, for:"follow"},
                success: function(response){
                    //console.log(response);
                    if($(e.target).is('.quick-list')){
                        _this.generateButtons_u('quick-list-f', $(e.target));
                        if($('#user').data('id') === my_id){
                            var increase_span = $('.following_count b');
                            var num = Number(increase_span.html());
                            increase_span.html(num+1);
                        }
                    }else{
                        _this.generateButtons_u('f');
                        var increase_span = $('.followers_count b');
                        var num = Number(increase_span.html());
                        increase_span.html(num+1);
                    }
                    _this.sent = false;
                },
                error: function(a,b,error){
                    console.log(error);
                    _this.sent = false;
                }
            })
        }
    }

    unfollowRequest(e, _this = this){
        e.preventDefault();
        if($(e.target).is('.quick-list')){
            var user_id = $(e.target).parent().data('id');
        }else{
            var user_id = $('#user').data('id');
        }
        var my_id = $('.user-list-item').data('id');
        if(!_this.sent){
            _this.sent = true;
            $.ajax({
                type: 'POST',
                url: './app/classes/reg-log.php',
                data: {my_id:my_id, user_id:user_id, for:"unfollow"},
                success: function(response){
                    //console.log(response);
                    if($(e.target).is('.quick-list')){
                        _this.generateButtons_u('quick-list-uf', $(e.target));
                        if($('#user').data('id') === my_id){
                            var increase_span = $('.following_count b');
                            var num = Number(increase_span.html());
                            increase_span.html(num-1);
                        }
                    }else{
                        _this.generateButtons_u('uf');
                        var increase_span = $('.followers_count b');
                        var num = Number(increase_span.html());
                        increase_span.html(num-1);
                    }
                    _this.sent = false;
                },
                error: function(a,b,error){
                    console.log(error);
                    _this.sent = false;
                }
            })
        }
    }

    showSaves(e, _this=this){
        if($('.saves-row').length){
            $('#user-posts .posts-row').fadeOut();
            $('.users-posts').removeClass('active');
            $('.my-saves').addClass('active');
            $('.saves-row').fadeIn();
            userPageScripts.fitImgSize();
        }else if($('.saves-row').not('.active')){
            var my_id = $('#user').data('id');
            if(!_this.sent){
                _this.sent = true;
                $.ajax({
                    type: 'POST',
                    url: './app/classes/reg-log.php',
                    data: {my_id:my_id, for:"saves", user_id: my_id},
                    success: function(response){
                        //console.log(response);
                        if(response !== ""){
                            var jsonn = JSON.parse(response);

                            $('#user-posts .row').fadeOut();
                            $('.users-posts').removeClass('active');
                            $('.my-saves').addClass('active');
                            var $div = $("<div>", {"class": "row saves-row"});
                            $("#user-posts .container").append($div);

                            if(jsonn.length) {
                                jsonn.forEach(item => {
                                    _this.loadSaves(item, _this);
                                })
                            }

                            userPageScripts.fitImgSize();
                            }else{
                                $('#user-posts .row').fadeOut();
                                $('.users-posts').removeClass('active');
                                $('.my-saves').addClass('active');
                                var $div = $("<div>", {"class": "row saves-row"});
                                $("#user-posts .container").append($div);
                            }
                    },
                    error: function(a,b,error){
                        console.log(error);
                        _this.sent = false;
                    }
                })
            }
        }else{
            return false;
        }
    }

    showPosts(){
        if($('.posts-row').not('.active')){
            $('#user-posts .saves-row').fadeOut();
            $('.my-saves').removeClass('active');
            $('.users-posts').addClass('active');
            $('.posts-row').fadeIn();
            userPageScripts.fitImgSize();
        }else{
            return false
        }
    }

    loadSaves(saves, _this){
        $('.saves-row').append(`<div class="col-4 img-box"><a class="hover-infos" href="post.php?post=${saves['unique_id']}">
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
            ${saves['post_likes']}
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
                ${saves['comment_count']}
            </span>
        </a>
        <img src="./people/images/${saves['image']}" alt=""></div>`);

        _this.sent = false;
    }

    showFollowers(e, _this=this){
        if(!_this.sent && Number($('.followers_count b').text()) !== 0){
            var user_id = $('#user').data('id');
             _this.sent = true;
            $.ajax({
                type: 'POST',
                url: './app/classes/reg-log.php',
                data: {user_id:user_id, for:"show_followers"},
                success: function(response){
                    //console.log(response);
                    var jsonn = JSON.parse(response);
                    $('.grayed_bg').css('display', 'block');
                    $('.followers_followings_heading h4').text('Followers');
                    var ul_list = $("<ul>");
                    $('.followers_followings').append(ul_list);
                    jsonn.forEach(item=>{
                        _this.listUsers(item);
                    })
                    _this.sent = false;
                },
                error: function(a,b,error){
                    console.log(error);
                    _this.sent = false;
                }
            })
        }
    }

    showFollowings(e, _this=this){
        if(!_this.sent && Number($('.following_count b').text()) !== 0){
            var user_id = $('#user').data('id');
             _this.sent = true;
            $.ajax({
                type: 'POST',
                url: './app/classes/reg-log.php',
                data: {user_id:user_id, for:"show_followings"},
                success: function(response){
                    //console.log(response);
                    var jsonn = JSON.parse(response);
                    $('.grayed_bg').css('display', 'block');
                    $('.followers_followings_heading h4').text('Following');
                    var ul_list = $("<ul>");
                    $('.followers_followings').append(ul_list);
                    $('.followers_followings ul').not(':first').remove();
                    jsonn.forEach(item=>{
                        _this.listUsers(item);
                    })
                    _this.sent = false;
                },
                error: function(a,b,error){
                    console.log(error);
                    _this.sent = false;
                }
            })
        }
    }

    listUsers(user){
        $('.share-container').remove();
        $('.followers_followings ul').append(`
        <li class="ff-person" data-id="${user['id']}">
            <div class="ff-person-img">
                <img src="${ user['avatar'] !== null ? `./people/profile-pics/${user['avatar']}` : `./assets/user-mini.png`}" alt="user">
            </div>
            <a href="user.php?user=${user['username']}"><div class="ff-person-info">
                <h5>${user['username']}</h5>
                <p>${user['name']}</p>
            </div></a>
            ${$('.user-list-item').data('id') == user['id'] ? "" : `${user['following'] === true ? `<button class="unfollow-btn quick-list">Unfollow</button>` : `<button class="follow-btn quick-list">Follow</button>`}`}
        </li>
        `)
    }

    closeFF(e){
        if($(e.target).is('.grayed_bg') || $(e.target).is('.followers_followings_heading span')){
            $('.followers_followings ul').remove();
            $('.share-container').remove();
            $('.grayed_bg').css('display','none');
        }
    }

    loadShareScreen(){
        $('.followers_followings ul').remove();
        //$('.share-container').remove();
        $('.grayed_bg').css('display', 'block');
        $('.followers_followings_heading h4').text('Share');
        $('.followers_followings').append(`
        <div class="share-container">
            <div class="image-preview">
                <img class="preview-image empty" alt="preview">
                <span class="empty-preview">Click to select image...</span>
            </div>
            <form class="share-form" action="user.php" method="post" enctype="multipart/form-data">
                <input type="file" class="share__select-image">
                <textarea class="share__descrp" cols="30" rows="3" placeholder="Description"></textarea>
                <button class="share__submit-btn" type="submit" value="Share"><img src="./assets/upload.png"> Share</button>
            </form>
        </div>
        `);
        //$('.share-container').not(':first').remove();
    }

    showPreview(e){
        const file = e.target.files[0];
        const fileType = e.target.files[0].type;

        if(file){
            if(fileType === 'image/png' || fileType === 'image/jpg' || fileType === 'image/jpeg'){
                $('.warn-msg').remove();

                const reader = new FileReader();
                $('.empty-preview').css("display", "none");
                $('.preview-image').removeClass('empty');
                $('.preview-image').addClass('loaded');

                reader.addEventListener("load", function(){
                    $('.preview-image').attr("src", this.result)
                });

                reader.readAsDataURL(file);
            }else{
                $('.followers_followings').append(`<p class="warn-msg" style="color: red;">${fileType} files can not be uploaded!</p>`);
            }
        }
    }

    shareNewPost(e, _this = this){
        e.preventDefault();
        if($('.share__select-image').val() !== ""){
            if(!_this.sent){
                _this.sent = true;
                var my_id = $('.user-list-item').data('id');
                var description = $('.share__descrp').val();
                var formData = new FormData();
                var files = $('.share__select-image')[0].files[0];
                formData.append('file', files);

                $.ajax({
                    type: 'POST',
                    url: './app/classes/post_actions.php',
                    data: formData,
                    cache:false,
                    contentType: false,
                    processData: false,
                    success: function(response){
                        console.log(response);
                        if(response !== 'error' && response !== 'Extension-error'){
                            $.ajax({
                                type: 'POST',
                                url: './app/classes/post_actions.php',
                                data: {my_id:my_id, description: description, image: response},
                                success: function (response_2) {
                                    window.location.reload();
                                }
                            })
                        }
                        _this.sent = false;
                    }
                })
            }
        }else{
            $('.followers_followings').append(`<p class="warn-msg" style="color: red;">Select an image file!</p>`);
        }
    }
}

var newUserPageScripts = new userPageScripts();