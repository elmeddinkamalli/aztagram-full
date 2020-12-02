class mainScripts{
    sent = false;

    constructor(){
        this.smallScreenSearchIcon = $('.small-screen-search-icon');
        this.searchForm = $('.search-form');
        this.searchInput = $('.search-input');
        this.searchContainer = $('.search-container');
        this.searchSpan = $('.search-span');
        this.searchSvg = $('.search-svg');
        this.events();
        this.reloadTimeForSearch = 1500;
        this.timeoutBegin = false;
        this.timer;
    }

    //Events
    events(){
        this.smallScreenSearchIcon.on('click', '.search-svg', this.openSearchForm.bind(this));
        this.searchForm.on('click', '.search-span', mainScripts.focusSearchInput.bind(this));
        this.searchForm.on('click', '.search-svg', mainScripts.focusSearchInput.bind(this));
        this.searchInput.on('click', mainScripts.focusSearchInput.bind(this));
        this.searchInput.focusout(this.detectTextInInput.bind(this));
        $(window).resize(this.showSearchOnResize.bind(this));
        //$(document).on("click", '#post-comment-svg', this.focusCommentInput.bind(this));
        //$(document).on("click", '.post-comment', this.focusCommentInput.bind(this));
        this.searchInput.on('keyup', this.getSearchResults.bind(this));
        this.searchInput.on('focus', () => {if(this.searchInput.val() !== ""){this.getSearchResults();}});
        $(document).on('click', this.closeAll.bind(this));
        $(document).on('click', this.closeNtfBar.bind(this));
        $(document).on('click', '.notifications_header_link', this.showLastNotifications.bind(this));
        $(document).on('click', '.message-btn', this.startNewDirect.bind());
    }

    //Methods
    openSearchForm(){
        if(!this.searchContainer.hasClass('active')){
            this.searchContainer.slideDown();
            this.searchContainer.addClass('active');
            mainScripts.focusSearchInput('', this);
        }else{
            this.searchContainer.slideUp();
            this.searchContainer.removeClass('active');
        }
    };

    static focusSearchInput(e, _this = this){
        _this.searchInput.focus();
        _this.searchSpan.css('display', 'none');
        if(window.innerWidth > 515){
            _this.searchSvg.css('left', '5px');
        }else{
            _this.searchSvg.css('left', '15px');
        }
    }

    detectTextInInput(){
        if(this.searchInput.val()){
            this.searchSpan.css('display', 'none');
            if(window.innerWidth > 515){
                this.searchSvg.css('left', '5px');
            }else{
                this.searchSvg.css('left', '15px');
            }
        }else{
            this.searchSpan.css('display', 'initial');
            this.searchSvg.css('left', '62px');
        }
    }

    showSearchOnResize(){
        if(window.innerWidth > 515){
            this.searchContainer.css('display', 'block');
            this.searchContainer.removeClass('active');
            this.searchSvg.css('left', '5px');
        }else{
            this.searchSvg.css('left', '62px');
            if(!this.searchContainer.hasClass('active')){
                this.searchContainer.css('display', '');
            }
        }
    }

    focusCommentInput(e){
        var commentBar;
        if($(e.target).is('#post-comment-svg')){
            var commentBar = $(e.target).parent().parent().find('.post-comment-form-container').find('input[type=hidden]');
        }else if($(e.target).is('.post-comment')){
            var commentBar = $(e.target).parent().parent().parent().find('.post-comment-form-container').find('input[type=hidden]');
        }
        commentBar.focus();
        console.log(commentBar);
    }

    getSearchResults(e, _this = this){
        clearTimeout(_this.timer);
        _this.timer = setTimeout(function(){
            var searchText = _this.searchInput.val();
            var trimmed_srctext = searchText.replace(/\s+/g, " ").trim();
            if(trimmed_srctext !== '') {
                $.ajax({
                    type: 'POST',
                    url: './app/classes/get_data_from_db.php',
                    data: {searchText:trimmed_srctext},
                    success: function(response){
                        //console.log(response);
                        var jsonn = JSON.parse(response);

                        $('.search-results-list').empty();
                        if(jsonn.length === 0){
                            _this.showSearchResults('empty');
                        }else{
                            jsonn.forEach(user=>{
                                _this.showSearchResults(user);
                            });
                        }
                    }
                })
            }
        }, _this.reloadTimeForSearch);
    }

    showSearchResults(user){
        $('.search-results').css('display', 'block');
        if(user === 'empty'){
            $('.search-results-list').append(`<h5 class="empty-src-results">Not found!</h5>`);
        }else{
            $('.search-results-list').append(`
            <div class="sr-person">
                <div class="sr-person-img">
                    <img src="${user['avatar'] !== null ? `./people/profile-pics/${user['avatar']}` : `./assets/user-mini.png`}" alt="user">
                </div>
                <a href="user.php?user=${user['username']}"><div class="sr-person-info">
                    <h5>${user['username']}</h5>
                    <p>${user['name']}</p>
                </div></a>
            </div>
            `);
        }
    }

    closeAll(e){
        if(!$(e.target).is('.search-input') &&
            !$(e.target).is('.search-results') &&
            !$(e.target).is('.search-svg') &&
            !$(e.target).is('.search-form') &&
            !$(e.target).is('.sr-person') &&
            !$(e.target).is('.sr-person-img') &&
            !$(e.target).is('.sr-person-img img') &&
            !$(e.target).is('.sr-person a') &&
            !$(e.target).is('.sr-person-info') &&
            !$(e.target).is('.sr-person-info h5') &&
            !$(e.target).is('.sr-person-info p') &&
            !$(e.target).is('.top-tick-result-bar') &&
            !$(e.target).is('.empty-src-results') &&
            !$(e.target).is('.search-results-list')){
            $('.search-results-list').empty();
            $('.search-results').css('display', 'none');
        }
    }

    showLastNotifications(e, _this= this){
        if(!_this.sent && !$('.notifications_container').is('.active')){
            _this.sent = true;

            $.ajax({
                type: 'POST',
                url: './app/classes/header.php',
                data: {for: 'notifications'},
                success: function(response){
                    //console.log(response);
                    var jsonn = JSON.parse(response);

                    $('.notifications_container').addClass('active');
                    $('.notifications_container').show();
                    if(jsonn.length != 0){
                        var ul = $("<ul>", {"class": "notifications-u-list"});
                        $('.notifications-here').append(ul);
                        jsonn.forEach(ntf => {
                            _this.addNotifications(ntf, true);
                        });
                    }else{
                        _this.addNotifications('', false);
                    }
                    $('.nav-item.active').addClass('wait');
                    $('.nav-item.active').removeClass('active');
                    $('.notifications_header').addClass('active');

                    _this.sent = false;
                }
            })
        }
    }

    addNotifications(ntf, have){
        if(have){
            $('.notifications-u-list').append(`
            <li class="ntf-list-el">
            <a class="ntf-post-link" href="${ ntf['post_unique_id'] !== undefined ? `post.php?post=${ntf['post_unique_id']}` : `user.php?user=${ntf['user_username']}`}"><img class="ntf-post-image" src="${ntf['post_image'] !== undefined ? `./people/images/${ntf['post_image']}` : `${ntf['user_image'] !== null ? `./people/profile-pics/${ntf['user_image']}` : `./assets/user-mini.png`}`}"></a>
            <a href="user.php?user=${ntf['user']}" class="ntf-username">${ntf['user']}</a> ${ntf['msg']}
            </li>
            `);
            $('.ntf_count').remove();
        }else{
            $('.notifications-here').append(`
            <div class="notifications-empty">
                <img src="./assets/empty-ntf.png">
                <p>Activity On Your Posts</p>
                <p>When someone likes or comments on one of your posts, you'll see it here.</p>
            </div>
            `);
        }
    }

    closeNtfBar(e){
        if(!$(e.target).is('.notifications_container') &&
           !$(e.target).is('.notifications_container *')){
            $('.notifications-here').empty();
            $('.notifications_container').hide();
            $('.notifications_container').removeClass('active');

            $('.nav-item.wait').addClass('active');
            $('.nav-item.wait').removeClass('wait');
            $('.notifications_header').removeClass('active');
        }
    }

    startNewDirect(e, _this=this){
        if($(e.target).parent().is('.ff-person')){
            var partner_id = $(e.target).parent().data('id');
        }else{
            var partner_id = $('#user').data('id');
        }

        $.ajax({
            type: 'POST',
            url: './app/classes/direct.php',
            data: {partner_id: partner_id, for: 'startNewDirect'},
            success: function(response){
                window.location.href = 'direct.php?directed='+response;
            }
        })
    }
}

var Scripts = new mainScripts();