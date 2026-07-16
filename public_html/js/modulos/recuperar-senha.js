$(document).ready(function() {
    var url = $('#urlBase').val();

    $('#gravar').click(function () {
        if (validaCampo($("input[name='email']"), false, true)) return false;

        var captcha = grecaptcha.getResponse();
        if(captcha == '') {
            $(".error_captcha").html("Marque o recaptcha.");
            return false;
        }else{
            $(".error_captcha").html("");
        }

        $.ajax({
            type: "POST",
            url: url+"ajax_verifica_recaptcha.php",
            cache: false,
            data: {captcha: captcha},
            context: this,
            dataType: 'json',
            beforeSend: function() {
                //
            },
            success: function(data){
                if(data.permissao){
                    $(".error_captcha").text("");
                    $('#gravar').hide();
                    $('.form').submit();
                }else{
                    $(".error_captcha").html("Erro inesperado! Atualize a pagina.");
                    return false;
                }
            },
            error: function(){
                console.debug('ops! ocorreu algum erro!');
            }
        });
        return false;
    });

    /*VALIDA EMAIL*/
    $('#email').blur(function(){
        var email = $("input[name='email']");

        if (validaCampo(email, '', false)) return false;

        if(email.val() != ''){
            $.ajax({
                type: "POST",
                url: url+"ajax_valida_email.php",
                cache: false,
                data: {email: email.val()},
                context: this,
                dataType: 'json',
                beforeSend: function() {
                    //
                },
                success: function(data){
                    if(data.permissao){
                        $(".error_email").text("");
                        $("#gravar").prop('disabled', false);
                        return false;
                    }else{
                        $(".error_email").text("Desculpe, e-mail inexistente!");
                        $("#gravar").prop('disabled', true);
                        return false;
                    }
                },
                error: function(){
                    alert('ops! ocorreu algum erro!');
                }
            });
        }
    });
});