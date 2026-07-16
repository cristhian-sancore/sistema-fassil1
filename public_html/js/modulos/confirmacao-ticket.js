$(document).ready(function() {
    var url = $('#urlBase').val();

    $('#gravar').click(function () {
        var captcha = grecaptcha.getResponse();
        if(captcha == '') {
            $(".error_captcha").html("Marque o recaptcha.");
            return false;
        }else{
            $(".error_captcha").html("");
        }

        $.confirm({
            boxWidth: '40%',
            useBootstrap: false,
            icon: 'fa fa-warning',
            title: 'Confirmação',
            content: 'Tem certeza que deseja <b>CONFIRMAR</b> o fechamento desse <b>TICKET</b>?',
            type: 'red',
            typeAnimated: true,
            buttons: {
                confirm:{
                    text: 'Confirmar',
                    btnClass: 'btn-red',
                    action: function() {
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
                    }
                },
                cancel:{
                    text: 'Cancelar',
                    action: function() {
                        //
                    }
                }
            }
        });
        return false;
    });
});