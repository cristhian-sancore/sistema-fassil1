$(document).ready(function() {
    $('#gravar').click(function(){
        var titulo = $("input[name='titulo']");
        if(titulo.val() == '') {
            titulo.focus();
            titulo.css('border', '1px solid #E33939');
            titulo.css('background', '#FFFFAF');
            return false;
        }else{
            titulo.css('border', '1px solid #d2d6de');
            titulo.css('background', '#FFFFFF');
        }
    });
});