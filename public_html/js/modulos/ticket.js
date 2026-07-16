$(document).ready(function() {
    var url = $('#urlBase').val();

    $('#gravar').click(function () {
        if (validaCampo($("select[name='prioridade']"), '0', false)) return false;
        if (validaCampo($("select[name='grupo']"), '0', false)) return false;
        if (validaCampo($("select[name='tipo']"), '0', false)) return false;
        if (validaCampo($("textarea[name='descricao']"), '', false)) return false;
        
        var captcha = grecaptcha.getResponse();
        if(captcha == '') {
            $(".error_captcha").html("Marque a caixa de selecao \"Nao sou um robo\".");
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