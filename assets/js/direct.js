//Direct

class directScript{
    sent = false;

    constructor() {
        this.eventsForDirect();
        this.reloadTimeForSearch = 1500;
        this.timeoutBegin = false;
        this.timer;
        this.scrollToBotttomOfMessages();
        this.keepDirectedsLive();
    }

    //Events
    eventsForDirect(){
        $(document).on("click", '.send-new-message', this.sendNewMessage.bind(this));
        $(document).on("click", '.grayed_bg', this.closeFF.bind(this));
        $(document).on('keyup', '.new-message-search-input', this.getNewMessageSearchResults.bind(this));
        $(document).on('submit', '.direct-form', this.sendMessage.bind(this));
        $(document).on('click', '.d-person', this.changeDirect.bind(this));
    }

    //Methods
    sendNewMessage(e, _this=this){
        $('.grayed_bg').css('display', 'block');
        $('.followers_followings_heading h4').text('Send New Message');
        $('.followers_followings').append(`
            <div class="new-message-search-box">
            <b>To:</b>
                <form action="#" class="new-message-search-form" onsubmit="event.preventDefault()">
                    <input type="text" class="new-message-search-input" placeholder="Search...">
                </form>
            </div>
        `);
    }

    getNewMessageSearchResults(e, _this = this){
        clearTimeout(_this.timer);
        _this.timer = setTimeout(function(){
            var searchText = $('.new-message-search-input').val();
            var trimmed_srctext = searchText.replace(/\s+/g, " ").trim();
            if(trimmed_srctext !== '') {
                $.ajax({
                    type: 'POST',
                    url: './app/classes/get_data_from_db.php',
                    data: {searchText:trimmed_srctext},
                    success: function(response){
                        //console.log(response);
                        var jsonn = JSON.parse(response);

                        $('.followers_followings ul').remove();
                        $('.followers_followings').append('<ul></ul>');

                        if(jsonn.length === 0){
                            _this.listUsers('empty');
                        }else{
                            jsonn.forEach(user=>{
                                _this.listUsers(user);
                            });
                        }
                    }
                })
            }
        }, _this.reloadTimeForSearch);
    }

    listUsers(user){
        if(user === 'empty'){
            $('.followers_followings ul').append(`<p>Not found!</p>`)
        }else{
            $('.followers_followings ul').append(`
            <li class="ff-person" data-id="${user['id']}">
                <div class="ff-person-img">
                    <img src="${ user['avatar'] !== null ? `./people/profile-pics/${user['avatar']}` : `./assets/user-mini.png`}" alt="user">
                </div>
                <a href="user.php?user=${user['username']}"><div class="ff-person-info">
                    <h5>${user['username']}</h5>
                    <p>${user['name']}</p>
                </div></a>
                ${$('.user-list-item').data('id') == user['id'] ? "" : `<button class="message-btn quick-list">Message</button>`}
            </li>
        `)
        }
    }

    closeFF(e){
        if($(e.target).is('.grayed_bg') || $(e.target).is('.followers_followings_heading span')){
            $('.followers_followings ul').remove();
            $('.new-message-search-box').remove();
            $('.grayed_bg').css('display','none');
        }
    }

    sendMessage(e, _this=this){
        e.preventDefault();
        var msg = $('.message-input').val();
        var trimmed_msg = msg.replace(/\s+/g, " ").trim();
        var direct_id = $('.message-input').data('direct-id');
        var u_id = Math.random().toString(36).replace('0.','id' || '');
        var last_msg = $('.msg_span_el').last().data('last-msg');
        var currentPartner = $('.direct-heading_m').data('id');
        if(trimmed_msg !== ""){
            $.ajax({
                type: 'POST',
                url: './app/classes/direct.php',
                data: {message:trimmed_msg, direct_id:direct_id},
                beforeSend: function(){
                    $('.messages-ul').append(`<li class="my_msg disabled ${u_id}"><span class="msg_span_el" data-last-msg="${parseInt(last_msg)+1}">${trimmed_msg}</span></span></li>`);
                    $('.messages-container').scrollTop($('.messages-container').prop("scrollHeight"));
                    $('.message-input').val('');
                },
                success: function(response){
                    //console.log(response);
                    if(response === 'sent'){
                        $('.my_msg.'+u_id).removeClass('disabled');
                        var element = $('div').find(`[data-id=${currentPartner}]`);
                        element.find('.last_msg').html(trimmed_msg.substr(0, 19) + '...');
                    }else{
                        $('.my_msg.'+u_id).remove();
                    }
                },
                error: function(){
                    $('.my_msg.'+u_id).remove();
                }
            })
        }
    }

    scrollToBotttomOfMessages(){
        if($('.messages-container').length){
            $('.messages-container').scrollTop($('.messages-container').prop("scrollHeight"));
        }
    }

    changeDirect(e, _this=this){
        if($(e.target).is('.d-person')){
            var partner_id = $(e.target).data('id');
            var element = $(e.target);
        }else if($(e.target).is('.d-person-img') || $(e.target).is('.d-person-info')){
            var partner_id = $(e.target).parent().data('id');
            var element = $(e.target).parent();
        }else if($(e.target).is('img') || $(e.target).is('p') || $(e.target).is('span') || $(e.target).is('h4')){
            var partner_id = $(e.target).parent().parent().data('id');
            var element = $(e.target).parent().parent();
        }

        if(!_this.sent){
            _this.sent = true;

            $.ajax({
                type: 'POST',
                url: './app/classes/direct.php',
                data: {for:'changeDirect', partner_id: partner_id},
                success: function(response){
                    //console.log(response);
                    var jsonn_d = JSON.parse(response);
                    var jsonn_m = JSON.parse(jsonn_d['messages']);
                    var my_id = $('.user-list-item').data('id');

                    var new_url = './direct.php?directed='+jsonn_d['unique_id'];
                    window.history.pushState(jsonn_d['unique_id'], 'Direct', new_url);

                    $('.selected-direct').empty();
                    $('.selected-direct').append(`
                    <div class="direct-heading_m" data-id="${jsonn_d['partner_1'] == my_id ? jsonn_d['partner_2'] : jsonn_d['partner_1']}">
                    <a class="direct-heading-user-avatar-link" href="user.php?user=${jsonn_d['username']}">
                            <img class="direct-selected-user-avatar" ${jsonn_d['avatar'] === null ? `src="./assets/user-mini.png"` : `src="./people/profile-pics/${jsonn_d['avatar']}"`}>
                        </a>
                        <a class="direct-heading-username" href="user.php?user=${jsonn_d['username']}">${jsonn_d['username']}</a>
                    </div>
                    <div class="messages-container">
                        <ul class="messages-ul"></ul>
                    </div>
                    <form class="direct-form" action="direct.php">
                        <input class="message-input" type="text" placeholder="Message..." data-direct-id="${jsonn_d['unique_id']}">
                        <input class="message-send-input" type="submit" value="Send">
                    </form>
                    `);

                    $('.messages-ul').empty();

                    for(var key in jsonn_m){
                        $('.messages-ul').append(`
                        <li class="${parseInt(jsonn_m[key]['from']) === parseInt(my_id) ? 'my_msg' : 'part_msg'}"><span class="msg_span_el" data-last-msg="${key}">${jsonn_m[key]['msg']}</span></li>
                        `);
                    }

                    if(parseInt($('.msg_count').html()) <= 1){
                        $('.msg_count').remove();
                    }else{
                        $('.msg_count').html(parseInt($('.msg_count').html())-1);
                    }

                    _this.scrollToBotttomOfMessages();

                    element.removeClass('not_seen');
                }
            })

            _this.sent = false;
        }
    }

    keepDirectedsLive(e, _this=this){
        setInterval(function(){
            $.ajax({
               type: 'POST',
               url: './app/classes/direct.php',
                data: {for: 'liveDirecteds'},
                success: function (response) {
                    //console.log(response);
                    var jsonn = JSON.parse(response);
                    var current_user_id = $('.direct-heading_m').data('id');

                    if(jsonn.length){
                        jsonn.forEach(item=>{
                            var element = $('div').find(`[data-id=${item['id']}]`);
                            if(parseInt(current_user_id) !== parseInt(item['id'])){
                                element.addClass('not_seen');
                            }
                            element.find('.last_msg').html(item['last_message']);
                        })
                    }
                }
            });
        },3000);

        setInterval(function(){
            if($('.direct-heading_m').length){
                var current_user_id = $('.direct-heading_m').data('id');
                var last_msg = $('.msg_span_el');
                if(last_msg.length){
                    last_msg = last_msg.last().data('last-msg');
                }else{
                    last_msg = 0;
                }
                $.ajax({
                    type: 'POST',
                    url: './app/classes/direct.php',
                    data: {for: 'liveChat', partner_id: current_user_id, last_msg: last_msg},
                    success: function(response){
                        //console.log(response);
                        var jsonn = JSON.parse(response);

                        if(jsonn.length){
                            for (var key in jsonn){
                                $('.messages-ul').append(`
                                <li class="part_msg"><span class="msg_span_el" data-last-msg="${parseInt(key) === 0 ? parseInt(last_msg)+1 : parseInt(last_msg)+parseInt(key)}">${jsonn[key]['msg']}</span></li>
                                `);

                                _this.scrollToBotttomOfMessages();

                                $.ajax({
                                    type: 'POST',
                                    url: './app/classes/direct.php',
                                    data: {for: 'makeSeen', partner_id: current_user_id}
                                })
                            }
                        }
                    }
                })
            }
        }, 3000)
    }
}

var newDirectScript = new directScript();