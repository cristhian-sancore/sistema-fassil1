$(document).ready(function() {
    var url = $('#urlBase').val();
    var url2 = $('#urlSite').val();

    $('#gravar').click(function () {
        // if (validaCampo($("input[name='nome']"), '', false)) return false;
        // if (validaCampo($("input[name='email']"), false, true)) return false;
        // if (validaCampo($("input[name='celular']"), '', false)) return false;
        // if (validaCampo($("select[name='setor']"), '0', false)) return false;
        // if (validaCampo($("input[name='assunto']"), '', false)) return false;
        // if (validaCampo($("textarea[name='mensagem']"), '', false)) return false;
        
        if (validaCampo($("input[name='nome']"), '', false)) return false;
        if (validaCampo($("input[name='firstname']"), '', false)) return false;
        if (validaCampo($("input[name='email']"), false, true)) return false;
        if (validaCampo($("input[name='celular']"), '', false)) return false;
         if (validaCampo($("input[name='phone']"), '', false)) return false;
        if (validaCampo($("select[name='setor']"), '0', false)) return false;
        if (validaCampo($("select[name='city']"), '0',false)) return false;
        if (validaCampo($("input[name='assunto']"), '', false)) return false;
        if (validaCampo($("textarea[name='mensagem']"), '', false)) return false;
        if (validaCampo($("input[name='entity']"), '', false)) return false;
        if (validaCampo($("input[name='setor']"), '', false)) return false;
        if (validaCampo($("input[name='cargo']"), '', false)) return false;

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
});