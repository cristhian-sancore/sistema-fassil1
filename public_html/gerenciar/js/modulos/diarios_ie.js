$(document).ready(function () {
    $("[data-mask]").inputmask();

    //Date picker
    $('.datepicker').datepicker({
        autoclose: true,
        format: "dd/mm/yyyy",
        language: "pt-BR"
    });

    var url = $('#url').val();

    $('#gravar').click(function(){
        if (validaCampo($("select[name='empresa']"), '0', false)) return false;
        if (validaCampo($("select[name='automovel']"), '0', false)) return false;
        if (validaCampo($("select[name='cidade']"), '0', false)) return false;
        if (validaCampo($("input[name='diasaida']"), '', false)) return false;
        if (validaCampo($("input[name='usuario']"), '', false)) return false;
        if (validaCampo($("input[name='kmsaida']"), '', false)) return false;
        if (validaCampo($("input[name='kmchegada']"), '', false)) return false;
        if (validaCampo($("input[name='os']"), '', false)) return false;
    });
    
    $("input[name='responsavel']").autocomplete({
        minLength: 3,
        source: function (request, response) {
            $.ajax({
                type: "POST",
                url: url + "/ajax_usuarios.php",
                dataType: "json",
                data: {busca: request.term},
                success: function (data) {
                    response(data);
                }
            });
        },
        focus: function (event, ui) {
            console.debug(ui.item.name);
            // $("#cliente").val(ui.item.name);
            // return false;
        },
        select: function (event, ui) {
            $("#usuario_id").val(ui.item.Usuario_ID);
            $("#usuario").val(ui.item.Usuario_Nome);
            return false;
        }
    }).data("ui-autocomplete")._renderItem = function (ul, item) {
        return $("<li>")
            .data("ui-autocomplete-item", item)
            .append("<a>" + item.Usuario_ID + " - " + item.Usuario_Nome + "</a>")
            .appendTo(ul);
    };
});