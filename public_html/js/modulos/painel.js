$(document).ready(function() {
    var url = $('#urlBase').val();

    //busca
    $(document).on('click', '#buscar', function(){
        var busca = $("input[name='busca']");

        pesquisa(busca.val());
        return false;
    });

    $(document).keypress(function(e) {
        $("input[name='busca']").focus();
    });
});