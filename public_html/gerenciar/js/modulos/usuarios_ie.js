$(document).ready(function() {
    $('#gravar').click(function(){
        if(validaCampo($("select[name='empresa']"), '0', false)) return false;
        if(validaCampo($("input[name='nome']"), '', false)) return false;
        if(validaCampo($("input[name='email']"), '', false)) return false;
        if(validaCampo($("input[name='login']"), '', false)) return false;

        var senha = $("input[name='senha']");
        var repetir_senha = $("input[name='repetir_senha']");

        if(senha.val() && !repetir_senha.val()) {
            if (senha.val() == '') {
                senha.focus();
                senha.css('border', '1px solid #E33939');
                senha.css('background', '#FFFFAF');
                return false;
            } else {
                senha.css('border', '1px solid #d2d6de');
                senha.css('background', '#FFFFFF');
            }
        }

        if (senha.val() && repetir_senha.val() != senha.val()) {
            repetir_senha.focus();
            repetir_senha.css('border', '1px solid #E33939');
            repetir_senha.css('background', '#FFFFAF');
            return false;
        } else {
            repetir_senha.css('border', '1px solid #d2d6de');
            repetir_senha.css('background', '#FFFFFF');
        }
    });
});