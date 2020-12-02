//Load more Ajax

class loadMoreAjax{
    sent = false;

    constructor() {
        this.events();
    }

    //events
    events() {
        //$(window).scroll(this.sendData.bind(this));
        //window.pageYOffset >= this.getElPos('last_post')
        if(!$('#user').length){
            window.addEventListener("scroll", this.sendData.bind(this), false);
        }else{
            window.addEventListener("scroll", this.sendUserData.bind(this), false);
        }
    }

    //methods
    sendData(e, _this = this){
        if ($(window).scrollTop() + $(window).height() == $(document).height()){
            if (!this.sent){
                var last_post_id = $('.last_post_data').data('id');
                this.sent = true;
                $.ajax({
                    type: 'POST',
                    url: './app/classes/load_new_p.php',
                    data: { last_post_id: last_post_id-1 },
                    success: function(response) {
                        //console.log(response);

                        var jsonn = JSON.parse(response);
                        //jsonn = jsonn.reverse();

                        if(jsonn.length) {

                            jsonn.forEach(item => {
                                let shared_time;
                                $.ajax({
                                    type: 'POST',
                                    url: './app/classes/sharedTime.php',
                                    data: {created_at: item['created_at']},
                                    success: function (time) {
                                        shared_time = time;
                                    }
                                }).then(function () {

                                    $('.post#last_post').removeAttr('id');
                                    $('.last_post_data').remove();

                                    if (item === jsonn[jsonn.length - 1]) {
                                        var lastone = true;
                                        _this.createNewPost(item, lastone, shared_time);
                                    } else {
                                        _this.createNewPost(item, 'false', shared_time);
                                    }

                                    _this.sent = false;
                                })
                            })

                        }
                    }
                });
            }
        }
    }

    sendUserData(e, _this = this){
        if ($(window).scrollTop() >= $(document).height() - $(window).height() - 100 && $('.users-posts').is('.active')){
            console.log("yeha");
            if (!_this.sent){
                _this.elements = [];
                _this.sent = true;

                var last_post_id = $('.last_post_data').data('id');
                var user_id = $('#user').data('id');

                $('.img-box#last_post').removeAttr('id');
                $('.last_post_data').remove();

                $.ajax({
                    type: 'POST',
                    url: './app/classes/load_new_p.php',
                    data: { last_post_id_user: last_post_id-1, user_id: user_id },
                    success: function(response) {
                        var jsonn = JSON.parse(response);
                        //jsonn = jsonn.reverse();

                        if(jsonn.length) {

                            jsonn.forEach(item => {

                                if (item === jsonn[jsonn.length - 1]) {
                                    var lastone = true;
                                    _this.createNewUserPost(item, lastone);
                                } else {
                                    var lastone = false;
                                    _this.createNewUserPost(item, lastone);
                                }
                            })
                        }
                    }
                });
            }
        }
    }

    getElPos(e){
        return document.getElementById(e).offsetTop;
    }

    createNewPost(post, lastone = false, shared_time){
        let shared = shared_time;
        var identity = 100000 + Math.floor(Math.random() * 999999);
        var $div = $("<div>", {id: `${lastone ? 'last_post' : ''}`, "class": "col post"});
        $div.html(`${lastone ? `<div class="last_post_data" data-id="${post['id']}" style="display: none;"></div>` : ''}
                <!-- Post Heading -->
                <div class="post-heading">
                    <a href="./user.php?user=${post['username']}" class="post-heading-link">
                        <img class="post-user-img" src="${ post['avatar'] !== null ? `./people/profile-pics/${post['avatar']}` : `./assets/user-mini.png`}" alt="user"><span>${post['username']}</span>
                    </a>
                    <div class="post-settings-points"><span></span><span></span><span></span></div>
                </div>

                <!-- Post Image -->
                <div class="post-img">
                    <img src="./people/images/${post['image']}" alt="post img">
                </div>

                <!-- Post Actions -->
                <div class="post-actions" data-id="${post['id']}">

                    <svg version="1.1" id="post-heart-svg" class="${post['liked']} like" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
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

                    <svg version="1.1" id="post-save-svg" class="${post['saved']} saved" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                        viewBox="0 0 594.8 669.6" style="enable-background:new 0 0 594.8 669.6;" xml:space="preserve">
                        <style type="text/css">
                            .post-save{fill:none;stroke:#262626;stroke-width:34px;stroke-miterlimit:10;}
                        </style>
                        <polygon class="post-save" points="33.5,31.6 33.5,637.5 299.3,330.4 564.6,637.5 564.6,31.6 "/>
                        </svg>

                </div>

                <!-- Post infos -->
                <div class="post-info">
                    <div class="post-likes"><span>${post['post_likes']}</span> likes</div>
                    <div class="post-author-title"><a href="user.php?user=${post['username']}" class="post-author">${post['username']}</a><span class="post-title"> ${post['description']}</span></div>
                </div>

                <!-- Post comments -->
                <div class="post-comments" id="${identity}">
                    ${post['comments']['comment_count'] > 2 ? `<div class="post-comments-view-more" data-id-com="${post['id']}">View all <span>${post['comments']['comment_count']}</span> comments</div>` : ''}
                    <div class="post-selected-comments">
                    
                        ${'comment-2' in post['comments'] ? `<div class="post-comment">
                            <a href="user.php?user=${post['comments']['comment-2']['c_author']}" class="post-comment-author">${post['comments']['comment-2']['c_author']}</a><span class="post-raw-comment"> ${post['comments']['comment-2']['text']}</span>
                            <div class="comment_like_box">
                            <input type="hidden" class="comment-id" data-id="${post['comments']['comment-2']['comment_id']}">
                            ${post['comments']['comment-2']['like_count'] !== 0 ? `<span class="comment_like_count">${post['comments']['comment-2']['like_count']}</span>` : ``}
                            <svg version="1.1" id="post-comment-svg-heart" class="like_this_comment ${post['comments']['comment-2']['liked'] === 'yes' ? 'liked' : 'not-liked'}" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                 x="0px" y="0px" viewBox="0 0 60 60" style="enable-background:new 0 0 60 60;" xml:space="preserve">
                                <style type="text/css">
                                    .post-comment-heart{fill:none;stroke:#000000;stroke-width:2px;stroke-linecap:round;stroke-linejoin:round;}
                                </style>
                                    <path class="post-comment-heart" d="M8.63,32.34L29.89,53.6l21.26-21.26c3.71-3.71,5.05-8.37,3.78-13.13c-1.46-5.45-6.1-10.09-11.55-11.55
                                        c-4.76-1.27-9.42,0.07-13.13,3.78l-0.35,0.35l-0.35-0.35c-2.64-2.64-5.82-4.01-9.19-4.01c-1.13,0-2.28,0.15-3.44,0.46
                                        C11.41,9.37,6.56,14.23,5.09,19.71C3.85,24.33,5.11,28.82,8.63,32.34z"/>
                                </svg>
                                </div>
                        </div>` : ''}
                        ${'comment-1' in post['comments'] ? `<div class="post-comment">
                            <a href="user.php?user=${post['comments']['comment-1']['c_author']}" class="post-comment-author">${post['comments']['comment-1']['c_author']}</a><span class="post-raw-comment"> ${post['comments']['comment-1']['text']}</span>
                            <div class="comment_like_box">
                            <input type="hidden" class="comment-id" data-id="${post['comments']['comment-1']['comment_id']}">
                            ${post['comments']['comment-1']['like_count'] !== 0 ? `<span class="comment_like_count">${post['comments']['comment-1']['like_count']}</span>` : ``}
                            <svg version="1.1" id="post-comment-svg-heart" class="like_this_comment ${post['comments']['comment-1']['liked'] === 'yes' ? 'liked' : 'not-liked'}" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                 x="0px" y="0px" viewBox="0 0 60 60" style="enable-background:new 0 0 60 60;" xml:space="preserve">
                                <style type="text/css">
                                    .post-comment-heart{fill:none;stroke:#000000;stroke-width:2px;stroke-linecap:round;stroke-linejoin:round;}
                                </style>
                                    <path class="post-comment-heart" d="M8.63,32.34L29.89,53.6l21.26-21.26c3.71-3.71,5.05-8.37,3.78-13.13c-1.46-5.45-6.1-10.09-11.55-11.55
                                        c-4.76-1.27-9.42,0.07-13.13,3.78l-0.35,0.35l-0.35-0.35c-2.64-2.64-5.82-4.01-9.19-4.01c-1.13,0-2.28,0.15-3.44,0.46
                                        C11.41,9.37,6.56,14.23,5.09,19.71C3.85,24.33,5.11,28.82,8.63,32.34z"/>
                                </svg>
                                </div>
                        </div>` : ''}
                    </div>

                    <div class="post-timeline">${shared}</div>

                </div>

                <!-- Post comment form -->
                <div class="post-comment-form-container">
                    <form action="#" method="post">
                        <input type="hidden" class="comment-input" id="${post['id']}">
                        <textarea class="comment-textarea" name="comment" placeholder="Add a comment..."></textarea>
                        <input class="comment-submit" type="submit" value="Post">
                    </form>
                </div>`);
        $('#posts .container .row').append($div);
    }

    createNewUserPost(post, lastone = false){
        var $div = $("<div>", {id: `${lastone ? 'last_post' : ''}`, "class": "col-4 img-box"});
        $div.html(`${lastone ? `<div class="last_post_data" data-id="${post['id']}" style="display: none;"></div>` : ''}
        <a class="hover-infos" href="post.php?post=${post['unique_id']}">
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
                            ${post['post_likes']}
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
                            ${post['comment_count']}
                        </span>
                </a>
                <img src="./people/images/${post['image']}" alt="">
            `);

        $("#user-posts .container .row").append($div);
        this.sent = false;

        userPageScripts.fitImgSize();
    }

}

var new_loadMore = new loadMoreAjax();