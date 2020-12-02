//Edit page

class editPageScript{
    sent = false;

    constructor() {
        this.eventsForEdit();
    }

    //Events
    eventsForEdit() {
        $('.edit-password').on('click', this.changeToPassPage);
        $('.edit-profile').on('click', this.changeToProfilePage);
        $('.change-avatar').on('click', () => {
            $('.change-avatar-file-inp').click()
        });
        $('.change-avatar-file-inp').on('change', this.changeAvatar);
    }


    //Methods
    changeToPassPage(){
        $('.edit-profile').removeClass('active');
        $('.edit-password').addClass('active');

        $('.edit-page-right-col').removeClass('active');
        $('.edit-page-pass-right-col').addClass('active');

        $('.msg.error').remove();

        var new_url = './edit.php?d=password';
        window.history.pushState('password', 'Edit', new_url);
    }

    changeToProfilePage(){
        $('.edit-password').removeClass('active');
        $('.edit-profile').addClass('active');

        $('.edit-page-pass-right-col').removeClass('active');
        $('.edit-page-right-col').addClass('active');

        $('.msg.error').remove();

        var new_url = './edit.php?d=general';
        window.history.pushState('general', 'Edit', new_url);
    }

    changeAvatar(e, _this=this){
        if($('.change-avatar-file-inp').val() !== ""){
            var my_id = $('.user-list-item').data('id');
            var formData = new FormData();
            var files = $('.change-avatar-file-inp')[0].files[0];
            formData.append('file', files);

            $.ajax({
                type: 'POST',
                url: './app/classes/user.php',
                data: formData,
                cache:false,
                contentType: false,
                processData: false,
                success: function(response){
                    console.log(response);
                    if(response == 'Extension-error'){
                        $('.change_avatar').append(`<p class="warn-msg" style="color: red;">File type is not appropriate!</p>`);
                    }else if(response == 'error'){
                        $('.change_avatar').append(`<p class="warn-msg" style="color: red;">Problem occured. Please, refresh the page and try again.</p>`);
                    }else{
                        window.location.reload();
                    }
                }
            })
        }
    }

}

var newEditPageScript = new editPageScript();