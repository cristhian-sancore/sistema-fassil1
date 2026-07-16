$(document).ready(function() {
    $(".colorpicker").colorpicker({
        format: 'hex'
    });

    var url = $('#url').val();

    $('#gravar').click(function(){
        if(validaCampo($("input[name='nome']"), '', false)) return false;
        if(validaCampo($("input[name='placa']"), '', false)) return false;
        if(validaCampo($("input[name='km']"), '', false)) return false;
    });
});