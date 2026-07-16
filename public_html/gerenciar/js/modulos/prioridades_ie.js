$(document).ready(function() {
    $(".colorpicker").colorpicker({
        format: 'hex'
    });

    var url = $('#url').val();

    $('#gravar').click(function(){
        if(validaCampo($("input[name='titulo']"), '', false)) return false;
        if(validaCampo($("input[name='background']"), '', false)) return false;
        if(validaCampo($("input[name='color']"), '', false)) return false;
    });
});