$(document).ready(function() {
    $("[data-mask]").inputmask();

    var url = $('#url').val();

    $('#gravar').click(function(){
        if(validaCampo($("select[name='estado']"), '0', false)) return false;
        if(validaCampo($("select[name='cidade']"), '0', false)) return false;
        if(validaCampo($("select[name='empresa']"), '0', false)) return false;
        if(validaCampo($("input[name='nome']"), '', false)) return false;
        if(validaCampo($("input[name='departamento']"), '', false)) return false;
        if(validaCampo($("input[name='login']"), '', false)) return false;
        if(validaCampo($("input[name='contato1']"), '', false)) return false;
    });

    $("select[name='estado']").change(function(){
        var cod = $(this).val();
        var subcat = $('#subcidade').val();

        $.ajax({
            type: "POST",
            url: url+"/ajax_cidade.php",
            cache: false,
            data: {cod: cod, subcat: subcat},
            beforeSend: function() {
                //
            },
            success: function(data){
                //$(".box_subcat").fadeIn();
                $("select[name='cidade']").html(data);
            },
            error: function(){
                alert('ops! ocorreu algum erro!');
            }
        });
    });

    if($("select[name='estado'] option:selected").val() != 0){
        $("select[name='estado']").trigger( "change" );
    }

    $("select[name='cidade']").change(function(){
        var cod = $(this).val();
        var subempresa = $('#subempresa').val();

        $.ajax({
            type: "POST",
            url: url+"/ajax_empresas.php",
            cache: false,
            data: {cod: cod, subempresa: subempresa},
            beforeSend: function() {
                //
            },
            success: function(data){
                //$(".box_subcat").fadeIn();
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
                url: url+"/ajax_valida_login.php",
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
                url: url+"/ajax_valida_email.php",
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