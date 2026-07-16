$(document).ready(function() {
    var url = $('#urlBase').val();

    $('#gravar').click(function () {
        if (validaCampo($("select[name='estado']"), '0', false)) return false;
        if (validaCampo($("select[name='cidade']"), '0', false)) return false;
        if (validaCampo($("select[name='empresa']"), '0', false)) return false;
        if (validaCampo($("input[name='nome']"), '', false)) return false;
        if (validaCampo($("input[name='departamento']"), '', false)) return false;
        if (validaCampo($("input[name='cargo']"), '', false)) return false;
        if (validaCampo($("input[name='email']"), false, true)) return false;
        if (validaCampo($("input[name='celular']"), '', false)) return false;
        if (validaCampo($("input[name='login']"), '', false)) return false;
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

    $("select[name='estado']").change(function(){
        var cod = $(this).val();

        $.ajax({
            type: "POST",
            url: url+"/ajax_cidades.php",
            cache: false,
            data: {cod: cod},
            beforeSend: function() {
                //
            },
            success: function(data){
                $("select[name='cidade']").html(data);
            },
            error: function(){
                alert('ops! ocorreu algum erro!');
            }
        });
    });

    $("select[name='cidade']").change(function(){
        var cod = $(this).val();

        $.ajax({
            type: "POST",
            url: url+"/ajax_empresas.php",
            cache: false,
            data: {cod: cod},
            beforeSend: function() {
                //
            },
            success: function(data){
                $("select[name='empresa']").html(data);
            },
            error: function(){
                alert('ops! ocorreu algum erro!');
            }
        });
    });

    /*VALIDA LOGIN*/
    $('#login').blur(function(){
        var login = $("input[name='login']");

        if (validaCampo(login, '', false)) return false;

        if(login.val() != ''){
            $.ajax({
                type: "POST",
                url: url+"ajax_valida_login.php",
                cache: false,
                data: {login: login.val()},
                context: this,
                dataType: 'json',
                beforeSend: function() {
                    //
                },
                success: function(data){
                    if(!data.permissao){
                        $(".error_login").text("");
                        $("#gravar").prop('disabled', false);
                        return false;
                    }else{
                        $(".error_login").text("Desculpe, login cadastrado!");
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
    /*FIM VALIDA LOGIN*/

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
                    if(!data.permissao){
                        $(".error_email").text("");
                        $("#gravar").prop('disabled', false);
                        return false;
                    }else{
                        $(".error_email").text("Desculpe, e-mail cadastrado!");
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
    /*FIM VALIDA EMAIL*/
});