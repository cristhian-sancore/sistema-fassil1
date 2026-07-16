$(document).ready(function() {
    $('#gravar').click(function () {
        if (validaCampo($("input[name='data_saida']"), '', false)) return false;
        if (validaCampo($("input[name='horario_saida']"), '', false)) return false;
        if (validaCampo($("input[name='data_retorno']"), '', false)) return false;
        if (validaCampo($("input[name='horario_retorno']"), '', false)) return false;
        if (validaCampo($("textarea[name='roteiro']"), '', false)) return false;
        if (validaCampo($("textarea[name='observacao']"), '', false)) return false;
        if (validaCampo($("input[name='nome']"), '', false)) return false;
        if (validaCampo($("input[name='email']"), '', false)) return false;
        if (validaCampo($("input[name='telefone']"), '', false)) return false;
        if (validaCampo($("input[name='celular']"), '', false)) return false;
    });
});