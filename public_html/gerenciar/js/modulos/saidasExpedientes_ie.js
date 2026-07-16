function EnviaAnexos(){
    var saida_id = $('#saida_id').val();

    $('#saidasExp_upload').data('uploadifive').settings.formData = { 'id'  : saida_id};
    $('#saidasExp_upload').uploadifive('upload');
}

$(document).ready(function () {
    $("[data-mask]").inputmask();

    //Date picker
    $('.datepicker').datepicker({
        autoclose: true,
        format: "dd/mm/yyyy",
        language: "pt-BR"
    });

    var url = $('#url').val();
    var urlBase = $('#urlBase').val();
    
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
    
    $("input[name='responsavel']").autocomplete({
        // minLength: 3,
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
    
    /*SELECIONA ANEXO*/
    // var $input = document.getElementById('anexo');
    // //
    // $input.addEventListener('change', function(){
    //     var name = this.value.split("fakepath\\");
    //     $('#anexo_nome').html("<i class='fa fa-cloud-upload'></i> "+name[1]);
    // });
    /*FIM SELECIONA ANEXO*/

    /*OS ANEXO*/
    $('#saidasExp_upload').uploadifive({
        'buttonClass'  : 'btn btn-primary',
        'buttonText'   : "<i class='fa fa-paperclip'></i>",
        'width'        : 37,
        'height'       : 34,
        'auto'         : false,
        'uploadScript' : url + "/ajax_saidasExpedientes_upload.php",
        'onUploadComplete' : function(file, data) {
            eval("d="+data);

            if(d != "erro"){
                var id = d[0];
                var caminho = d[1];
                var nome = d[2];

                $.ajax({
                    type: "POST",
                    url: url+"/ajax_anexo_thumb.php",
                    cache: false,
                    data: {id: id, caminho: caminho, nome: nome},
                    dataType: 'json',
                    beforeSend: function() {
                        //
                    },
                    success: function(data) {
                        $('.box_anexos').find('p').remove();
                        $('.box_anexos').append(data.template);
                    },
                    error: function() {
                        alert("Ops, erro ajax...");
                    }
                });
            }
            $('.complete').fadeOut();
        }
    });

    $(document).on('click', '.anexo_exc', function(){
        var id = $(this).attr('id');

        $.ajax({
            type: "POST",
            url: url+"/ajax_anexo_excluir.php",
            cache: false,
            data: {id: id},
            dataType: 'json',
            context: this,
            beforeSend: function() {
                //
            },
            success: function(data){
                $(this).parent('.anexo').fadeOut();
            },
            error: function(){
                console.debug("Ops, erro ajax...");
            }
        });
    });
    
});