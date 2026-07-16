$(document).ready(function () {
    $("[data-mask]").inputmask();

    //Date picker
    $('.datepicker').datepicker({
        autoclose: true,
        format: "dd/mm/yyyy",
        language: "pt-BR"
    });

    var url = $('#url').val();
    
    $('#gravar, #finalizar').click(function () {
        if (validaCampo($("select[name='prioridade']"), '0', false)) return false;
        if (validaCampo($("select[name='grupo']"), '0', false)) return false;
        if (validaCampo($("select[name='tipo']"), '0', false)) return false;
        if (validaCampo($("input[name='cliente']"), '', false)) return false;
        if (validaCampo($("input[name='cliente_id']"), '', false)) return false;
        if (validaCampo($("input[name='data_inicio']"), '', false)) return false;
        if (validaCampo($("input[name='horas_inicio']"), '', false)) return false;
        if (validaCampo($("textarea[name='descricao']"), '', false)) return false;
    });

    $("input[name='cliente']").autocomplete({
        minLength: 3,
        source: function (request, response) {
            $.ajax({
                type: "POST",
                url: url + "/ajax_clientes.php",
                dataType: "json",
                data: {busca: request.term},
                success: function (data) {
                    $(".info_cliente").empty();
                    response(data);
                }
            });
        },
        focus: function (event, ui) {
            // console.debug(ui.item.name);
            // $("#cliente").val(ui.item.name);
            // return false;
        },
        select: function (event, ui) {
            $("#cliente_id").val(ui.item.Cliente_ID);
            $("#cliente").val(ui.item.Cliente_Nome);

            var dados = "<address><b>Login</b>: "+ui.item.Cliente_Login+"<br><b>Departamento:</b> "+ui.item.Cliente_Departamento+"<br><b>Empresa/Orgão:</b> "+ui.item.CE_Nome+"<br><b>Cidade:</b> "+ui.item.Cidade_Nome+" - "+ui.item.Estado_UF+"<br><b>Contato:</b> "+ui.item.Cliente_Contato1;

            if(ui.item.Cliente_Contato2) {
                dados += "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+ui.item.Cliente_Contato2;
            }

            if(ui.item.Cliente_Contato3) {
                dados += "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+ui.item.Cliente_Contato3;
            }

            if(ui.item.Cliente_Email){
                dados += "<br><b>E-mail:</b> "+ui.item.Cliente_Email+"</address>";
            }

            $(".info_cliente").append(dados);
            return false;
        }
    }).data("ui-autocomplete")._renderItem = function (ul, item) {
        return $("<li>")
            .data("ui-autocomplete-item", item)
            .append("<a>" + item.Cliente_ID + " - " + item.Cliente_Nome + " - " + item.Cliente_Contato1 + "</a>")
            .appendTo(ul);
    };

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
            // console.debug(ui.item.name);
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