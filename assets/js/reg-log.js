
class reg_log{
    constructor() {
        this.eventsForRegLog();
    }

    eventsForRegLog(){
        $("input").on('keyup', this.activateRegisterButton.bind(this));
    }

    activateRegisterButton(){
        if($('#register-page').length){
            if($("input[type='email']").val() !== "" &&
                $("input[type='text']").val() !== "" &&
                $("input[type='password']").val() !== ""){
                $('.sign-up-btn').css('background', '#0095f6');
                $('.sign-up-btn').removeAttr('disabled');
            }else{
                $('.sign-up-btn').css('background', '#B5E0FD');
                $('.sign-up-btn').attr('disabled', 'disabled');
            }
        }else if($('#login-page').length){
            if($("input[type='text']").val() !== "" &&
                $("input[type='password']").val() !== ""){
                $('.log-in-btn').css('background', '#0095f6');
                $('.log-in-btn').removeAttr('disabled');
            }else{
                $('.log-in-btn').css('background', '#B5E0FD');
                $('.log-in-btn').attr('disabled', 'disabled');
            }
        }
    }
}

var newRegLogScript = new reg_log();
