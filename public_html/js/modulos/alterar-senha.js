$(document).ready(function() {
    var url = $('#urlBase').val();

    $('#gravar').click(function () {
        if (validaCampo($("input[name='senha']"), '', false)) return false;
        var senha = $("input[name='senha']").val();
        var senha2 = $("input[name='senha2']");
        if(senha != ''){
            if (validaCampo(senha2, '', false)) return false;
            if(senha != senha2.val()){
                senha2.focus();
                senha2.css('border', '1px solid #E33939');
                senha2.css('background', '#FFFFAF');
                return false;
            }else{
                senha2.css('border', '1px solid #cccccc');
                senha2.css('background', '#F2F2F2');
            }
        }
        
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