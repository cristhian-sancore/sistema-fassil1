$(document).ready(function () {
    var urlBase = $('#url').val();

    //CKEDITOR

    $("[data-mask]").inputmask();

    //Date picker
    $('.datepicker').datepicker({
        autoclose: true,
        format: "dd/mm/yyyy",
        language: "pt-BR"
    });

    var url = $('#url').val();

    $('#gravar').click(function () {
        if(validaTextarea($("#introducao"))) return false;
        if(validaTextarea($("#descricao"))) return false;
        if(validaTextarea($("#resultado"))) return false;
        if(validaTextarea($("#orientacao"))) return false;
        if(validaTextarea($("#observacao"))) return false;
        if(validaTextarea($("#conclusao"))) return false;
    });

    $("select[name='relatorio']").change(function(){
        var id = $('#os').val();

        var rel = $("select[name='relatorio']").val();
        if(rel == 2){
            $(".box_relatorio").fadeIn();
        }else{
            $(".box_relatorio").fadeOut();
        }

        $.ajax({
            type: "POST",
            url: urlBase+"/ajax_os_relatorio.php",
            cache: false,
            data: {id: id, rel: rel},
            dataType: 'json',
            context: this,
            beforeSend: function() {
                //
            },
            success: function(data){
                //
            },
            error: function(){
                console.debug("Ops, erro ajax...");
            }
        });
    });

    $("select[name='relatorio']").trigger("change");

    $(document).on('click', '.as_exc', function(){
        var id = $(this).attr('id');

        $.ajax({
            type: "POST",
            url: urlBase+"/ajax_assinatura_excluir.php",
            cache: false,
            data: {id: id},
            dataType: 'json',
            context: this,
            beforeSend: function() {
                //
            },
            success: function(data){
                $(this).parents('.linha_assinatura').fadeOut();
            },
            error: function(){
                console.debug("Ops, erro ajax...");
            }
        });
    });

    $(document).on('click', '.as_gravar', function(){
        var id = $("#as_nome_id").val();
        var os = $("#os_id").val();

        if (validaCampo($("#as_nome"), '', false)) return false;

        $.ajax({
            type: "POST",
            url: urlBase+"/ajax_assinatura_gravar.php",
            cache: false,
            data: {id: id, os: os},
            dataType: 'json',
            context: this,
            beforeSend: function() {
                //
            },
            success: function(data){
                location.reload();
            },
            error: function(){
                console.debug("Ops, erro ajax...");
            }
        });
    });

    $("input[name='as_nome']").autocomplete({
        minLength: 3,
        source: function (request, response) {
            $.ajax({
                type: "POST",
                url: urlBase + "/ajax_clientes.php",
                dataType: "json",
                data: {busca: request.term},
                success: function (data) {
                    response(data);
                }
            });
        },
        select: function (event, ui) {
            $("#as_nome_id").val(ui.item.Cliente_ID);
            $("#as_nome").val(ui.item.Cliente_Nome);
            $("#as_cargo").val(ui.item.Cliente_Cargo);
            return false;
        }
    }).data("ui-autocomplete")._renderItem = function (ul, item) {
        return $("<li>")
            .data("ui-autocomplete-item", item)
            .append("<a>" + item.Cliente_ID + " - " + item.Cliente_Nome + " - "+item.Cidade_Nome+"</a>")
            .appendTo(ul);
    };
});