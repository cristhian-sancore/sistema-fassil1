$(document).ready(function() {
    $('#gravar').click(function(){
        if(validaCampo($("input[name='nome']"), '', false)) return false;
    });

    $('.all').on('ifChecked', function(event){
        $(this).parents('.box_permissao').find('.all_check').iCheck('check');
    });

    $('.all').on('ifUnchecked', function(event){
        $(this).parents('.box_permissao').find('.all_check').iCheck('uncheck');
    });
});