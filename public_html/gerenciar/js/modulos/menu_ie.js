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

        var link = $("input[name='link']");
        if(link.val() == '') {
            link.focus();
            link.css('border', '1px solid #E33939');
            link.css('background', '#FFFFAF');
            return false;
        }else{
            link.css('border', '1px solid #d2d6de');
            link.css('background', '#FFFFFF');
        }

        var icon = $("input[name='icon']");
        if(icon.val() == '') {
            icon.focus();
            icon.css('border', '1px solid #E33939');
            icon.css('background', '#FFFFAF');
            return false;
        }else{
            icon.css('border', '1px solid #d2d6de');
            icon.css('background', '#FFFFFF');
        }
    });
});