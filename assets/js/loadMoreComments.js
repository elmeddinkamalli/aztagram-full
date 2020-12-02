//Load more Ajax

class loadMoreComments{
    sent = false;

    constructor() {
        this.events();
    }

    //events
    events() {
        $(document).on('click', '.post-comments-view-more', this.sendData.bind(this));
    }

    //methods
    sendData(e, _this = this){
            if (!this.sent){
                var clicked_btn = $(e.target);
                var post_id = clicked_btn.data('id-com');
                this.sent = true;

                $.ajax({
                    type: 'GET',
                    url: './app/classes/load_new_c.php',
                    data: { post_id: post_id },
                    success: function(response) {
                        //console.log(response);

                        var jsonn = JSON.parse(response);
                        //jsonn = jsonn.reverse();
                        var identity = clicked_btn.parents('.post-comments').attr('id');

                        $('#'+identity+' '+'.post-selected-comments').html('');
                        clicked_btn.remove();

                        if(jsonn.length) {
                            jsonn.forEach(item => {
                                _this.createNewComment(item, identity);

                            })
                        }

                        _this.sent = false;
                    }
                });
            }
    }

    createNewComment(comment, identity){
        var identity = '#' + identity;
        var $div = $("<div>", {"class": "post-comment"});
        $div.html(`<a href="user.php?user=${comment['username']}"><span class="post-comment-author">${comment['username']}</span></a><span class="post-raw-comment"> ${comment['comment']}</span>
                <div class="comment_like_box">
                            <input type="hidden" class="comment-id" data-id="${comment['id']}">
                            ${comment['like_count'] !== 0 ? `<span class="comment_like_count">${comment['like_count']}</span>` : ``}
                            <svg version="1.1" id="post-comment-svg-heart" class="like_this_comment ${comment['liked'] === 'yes' ? 'liked' : 'not-liked'}" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                 x="0px" y="0px" viewBox="0 0 60 60" style="enable-background:new 0 0 60 60;" xml:space="preserve">
                                <style type="text/css">
                                    .post-comment-heart{stroke:#000000;stroke-linecap:round;stroke-linejoin:round;}
                                </style>
                                <path class="post-comment-heart" d="M8.63,32.34L29.89,53.6l21.26-21.26c3.71-3.71,5.05-8.37,3.78-13.13c-1.46-5.45-6.1-10.09-11.55-11.55
                                    c-4.76-1.27-9.42,0.07-13.13,3.78l-0.35,0.35l-0.35-0.35c-2.64-2.64-5.82-4.01-9.19-4.01c-1.13,0-2.28,0.15-3.44,0.46
                                    C11.41,9.37,6.56,14.23,5.09,19.71C3.85,24.33,5.11,28.82,8.63,32.34z"/>
                                </svg>
                        </div>`);
        $(identity+' '+'.post-selected-comments').append($div);
    }

}

var new_loadMoreComments = new loadMoreComments();