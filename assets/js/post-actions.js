//Post actions

class postActions{
    sent = false;

    constructor(){
        this.specialEvents();
    }

    //Events
    specialEvents(){
        $(document).on("click", '#post-heart-svg', this.giveHeart.bind(this));
        $(document).on("click", '.comment-submit', this.addComment.bind(this));
        $(document).on("click", '#post-save-svg', this.savePost.bind(this));
        $(document).on("click", '.like_this_comment', this.likeComment.bind(this));
        $(document).on("click", '.post-likes', this.showLikes.bind(this));
        //$(document).on("click", '.unfollow-btn', this.unfollowRequest.bind(this));
        //$(document).on("click", '.follow-btn', this.followRequest.bind(this));
        $(document).on("click", '.grayed_bg', this.closeFF.bind(this));
    }

    //Methods
    giveHeart(e, _this=this){
        e.preventDefault();
        if(!_this.sent){
            _this.sent = true;

            if($(e.target).is('#post-heart-svg')){
                var post_id = $(e.target).parent().data('id');
                var element = $(e.target);
            }else if($(e.target).is('.post-heart')){
                var post_id = $(e.target).parent().parent().data('id');
                var element = $(e.target).parent();
            }

            $.ajax({
                type: 'POST',
                url: './app/classes/post_actions.php',
                data: {post_id:post_id, for:'heart'},
                success: function(response){
                    //console.log(response);
                    
                    var increase_span = element.parent().next().find('.post-likes span');

                    if(response === 'liked'){
                        element.addClass('yes');
                        element.removeClass('no');
                        var num = Number(increase_span.html());
                        increase_span.html(num+1);
                    }else if(response === 'dissliked'){
                        element.removeClass('yes');
                        element.addClass('no');
                        var num = Number(increase_span.html());
                        increase_span.html(num-1);
                    }

                    _this.sent = false;
                }
            })
        }
    }

    addComment(e, _this=this){
        e.preventDefault();

        if(!_this.sent){
            _this.sent = true;
            var raw_comment = $(e.target).parent().find('.comment-textarea').val();
            var post_id = $(e.target).parent().find('.comment-input').attr('id');
            var trimmed_comment = raw_comment.replace(/\s+/g, " ").trim();

            if(trimmed_comment === ""){
                return false;
            }else{
                $.ajax({
                    type: 'POST',
                    url: './app/classes/post_actions.php',
                    data: {comment:trimmed_comment, post_id:post_id, for:'comment'},
                    success: function(response){
                        //console.log(response);
                        var jsonn = JSON.parse(response);
                        var identity = $(e.target).parent().parent().prev().attr('id');

                        _this.createNewComment(jsonn, identity);
                        $(e.target).parent().find('.comment-textarea').val("");

                        _this.sent = false;
                    }
                })
            }
        }
    }

    likeComment(e, _this=this){
        if(!_this.sent){
            _this.sent = true;
            if($(e.target).is('svg')){
                var comment_id = $(e.target).parent().find('.comment-id').data('id');
                var element = $(e.target).parent();
            }else if($(e.target).is('path')){
                var comment_id = $(e.target).parent().parent().find('.comment-id').data('id');
                var element = $(e.target).parent().parent();
            }
            $.ajax({
                type: 'POST',
                url: './app/classes/post_actions.php',
                data: {comment_id:comment_id, comment_like_unlike: true},
                success: function(response){
                    //console.log(response);
                    
                    if(response === 'liked'){
                        _this.createCommentLike(element, 'like');
                    }else if(response === 'unliked'){
                        _this.createCommentLike(element, 'unlike');
                    }

                    _this.sent = false;
                }
            })
        }
    }

    savePost(e, _this=this){
        e.preventDefault();

        if(!_this.sent){
            _this.sent = true;

            if($(e.target).is('#post-save-svg')){
                var post_id = $(e.target).parent().data('id');
                var element = $(e.target);
            }else if($(e.target).is('.post-save')){
                var post_id = $(e.target).parent().parent().data('id');
                var element = $(e.target).parent();
            }

            $.ajax({
                type: 'POST',
                url: './app/classes/post_actions.php',
                data: {post_id:post_id, for:'save'},
                success: function(response){
                    //console.log(response);
                    if(response === 'saved'){
                        element.removeClass('no');
                        element.addClass('yes');
                    }else if(response === 'not-saved'){
                        element.removeClass('yes');
                        element.addClass('no');
                    }
                    _this.sent = false;
                }
            })
        }
    }

    createNewComment(comment, identity){
        var identity = '#' + identity;
        var $div = $("<div>", {"class": "post-comment"});
        $div.html(`<a href="user.php?user=${comment['author']}"><span class="post-comment-author">${comment['author']}</span></a><span class="post-raw-comment"> ${comment['comment']}</span>
                        <div class="comment_like_box">
                        <input type="hidden" class="comment-id" data-id="${comment['id']}">
                            <svg version="1.1" id="post-comment-svg-heart" class="like_this_comment not-liked" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                 x="0px" y="0px" viewBox="0 0 60 60" style="enable-background:new 0 0 60 60;" xml:space="preserve">
                                <style type="text/css">
                                    .post-comment-heart{fill:none;stroke-width:2px;stroke:#000000;stroke-linecap:round;stroke-linejoin:round;}
                                </style>
                                <path class="post-comment-heart" d="M8.63,32.34L29.89,53.6l21.26-21.26c3.71-3.71,5.05-8.37,3.78-13.13c-1.46-5.45-6.1-10.09-11.55-11.55
                                    c-4.76-1.27-9.42,0.07-13.13,3.78l-0.35,0.35l-0.35-0.35c-2.64-2.64-5.82-4.01-9.19-4.01c-1.13,0-2.28,0.15-3.44,0.46
                                    C11.41,9.37,6.56,14.23,5.09,19.71C3.85,24.33,5.11,28.82,8.63,32.34z"/>
                                </svg>
                        </div>`);
        $(identity+' '+'.post-selected-comments').append($div);
    }

    createCommentLike(e, f){
        if(f === 'like'){
            if(e.find('.comment_like_count').length){
                var span = e.find('.comment_like_count');
                var num = Number(span.html());
                span.html(num+1);
            }else{
                var span = $("<span>", {"class": "comment_like_count"});
                span.html(1);
                e.prepend(span);
            }
            e.find('.like_this_comment').removeClass('not-liked');
            e.find('.like_this_comment').addClass('liked');
        }else if(f === 'unlike'){
            var span = e.find('.comment_like_count');
            if(Number(span.html()) === 1){
                span.remove();
            }else{
                var num = Number(span.html());
                span.html(num-1);
            }
            e.find('.like_this_comment').removeClass('liked');
            e.find('.like_this_comment').addClass('not-liked');
        }
    }

    showLikes(e, _this=this){
        if(!_this.sent){
            if($(e.target).is('div')){
                var curValue = Number($(e.target).find('span').text());
                var post_id = $(e.target).parent().prev().data('id');
            }else if($(e.target).is('span')){
                var curValue = Number($(e.target).text());
                var post_id = $(e.target).parent().parent().prev().data('id');
            }
            if(curValue !== 0){
                _this.sent = true;
                $.ajax({
                    type: 'POST',
                    url: './app/classes/post_actions.php',
                    data: {post_id:post_id, for:"show_likes"},
                    success: function(response){
                        //console.log(response);
                        var jsonn = JSON.parse(response);
                        $('.grayed_bg').css('display', 'block');
                        $('.show-likes-heading h4').text('Likes');
                        jsonn.forEach(item=>{
                            _this.listUsers(item);
                        });
                        _this.sent = false;
                    },
                    error: function(a,b,error){
                        console.log(error);
                        _this.sent = false;
                    }
                })
            }
        }
    }

    listUsers(user){
        $('.show-likes ul').append(`
        <li class="ff-person" data-id="${user['id']}">
            <div class="ff-person-img">
                <img src="${ user['avatar'] !== null ? `./people/profile-pics/${user['avatar']}` : `./assets/user-mini.png`}" alt="user">
            </div>
            <a href="user.php?user=${user['username']}"><div class="ff-person-info">
                <h5>${user['username']}</h5>
                <p>${user['name']}</p>
            </div></a>
            ${$('.user-list-item').data('id') == user['id'] ? "" : `${user['following'] === true ? `<button class="unfollow-btn quick-list">Unf</button>` : `<button class="follow-btn quick-list">Follow</button>`}`}
        </li>
        `)
    }

    closeFF(e){
        if($(e.target).is('.grayed_bg') || $(e.target).is('.show-likes-heading span')){
            $('.show-likes ul').empty();
            $('.grayed_bg').css('display','none');
        }
    }

    // followRequest(e, _this = this){
    //     e.preventDefault();
    //     var user_id = $(e.target).parent().data('id');
    //     var my_id = $('.user-list-item').data('id');

    //     if(!_this.sent){
    //         _this.sent = true;
    //         $.ajax({
    //             type: 'POST',
    //             url: './app/classes/reg-log.php',
    //             data: {my_id:my_id, user_id:user_id, for:"follow"},
    //             success: function(response){
    //                 console.log(response);
    //                 if($(e.target).is('.quick-list')){
    //                     _this.generateButtons('quick-list-f', $(e.target));
    //                 }else{
    //                     _this.generateButtons('f');
    //                 }
    //                 _this.sent = false;
    //             },
    //             error: function(a,b,error){
    //                 console.log(error);
    //                 _this.sent = false;
    //             }
    //         })
    //     }
    // }

    // unfollowRequest(e, _this = this){
    //     e.preventDefault();
    //     var user_id = $(e.target).parent().data('id');
    //     var my_id = $('.user-list-item').data('id');

    //     if(!_this.sent){
    //         _this.sent = true;
    //         $.ajax({
    //             type: 'POST',
    //             url: './app/classes/reg-log.php',
    //             data: {my_id:my_id, user_id:user_id, for:"unfollow"},
    //             success: function(response){
    //                 console.log(response);
    //                 if($(e.target).is('.quick-list')){
    //                     _this.generateButtons('quick-list-uf', $(e.target));
    //                 }else{
    //                     _this.generateButtons('uf');
    //                 }
    //                 _this.sent = false;
    //             },
    //             error: function(a,b,error){
    //                 console.log(error);
    //                 _this.sent = false;
    //             }
    //         })
    //     }
    // }

    // generateButtons(d,e){
    //     var person_element = e.parent();
    //     if(d === 'quick-list-f'){
    //         e.remove();
    //         var button = $("<button>", {"class": "unfollow-btn quick-list", "value": "Unfollow"});
    //         button.html("Unf");
    //         person_element.append(button);
    //     }else if(d === 'quick-list-uf'){
    //         e.remove();
    //         var button = $("<button>", {"class": "follow-btn quick-list", "value": "Follow"});
    //         button.html("Follow");
    //         person_element.append(button);
    //     }
    // }
}

var newPostActions = new postActions();