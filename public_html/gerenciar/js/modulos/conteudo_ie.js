categoria = 0;
metadado = $('#metadado').attr('value');
session_id = $('#session_id').attr('value');
acao = $('#acao').val();

function timeMsg(){
    setTimeout(function(){
        $('.msg').fadeOut();
    }, 5000);
}

function enviar() {
    var urlBase = $('#url').val();

    var Conteudo_ID = $("#Conteudo_ID").val();
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

    var cidade = $("select[name='cidade'] option:selected").val();
    var acao = $("input[name='acao']").val();
    var status = $("input[name='status']:checked").val();
    var data_inicio = $("input[name='data_inicio']");
    var data_fim = $("input[name='data_fim']");
    var descricao = $(".descricao");
    var conteudo = $(".conteudo");

    var categoria = [];
    $('.categoria :selected').each(function(i, selected){
        categoria[i] = $(selected).val();
    });

    $.ajax({
        type: "POST",
        url: urlBase+"/ajax_conteudo.php",
        cache: false,
        async: false,
        data: {Conteudo_ID: Conteudo_ID, acao: acao, cidade: cidade, categoria: categoria, status: status, data_inicio: data_inicio.val(), data_fim: data_fim.val(), titulo: titulo.val(), descricao: descricao.val(), conteudo: conteudo.val()},
        dataType: 'json',
        beforeSend: function() {
            /* empty */
        },
        success: function(data){
            Conteudo_ID = data.Conteudo_ID;
            $('#acao').attr('value', 'e');
            $('#Conteudo_ID').attr('value', Conteudo_ID);

            $('.link').show();

            var fila = $('#file_uploadQueue').find('div[class=uploadifyQueueItem]').size();
            if(fila == 0) {
                //timeMsg();
                $('#msg').html("Dados salvos com Sucesso!").show();
                return false;
            }
        },
        error: function(){
            alert("Ops, erro ajax...");
        }
    });

    $('#file_upload').data('uploadifive').settings.formData = { 'id'  : Conteudo_ID, 'metadado' : metadado, 'session_id' : session_id, 'acao' : acao};
    $('#file_upload').uploadifive('upload');
}

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

    /*ENVIO DE MIDIAS*/
    var urlBase = $('#url').val();
    var diretorio = $('#diretorio').val();

    $('#file_upload').uploadifive({
        'buttonClass'  : 'btn btn-danger',
        'buttonText'   : "<i class='fa fa-paperclip'></i> SELECIONE MÍDIAS",
        'width'        : 200,
        'height'       : 50,
        'auto'         : false,
        'uploadScript' : urlBase+"/conteudo_ie.php",
        'onUploadComplete' : function(file, data) {
            // !* uma vez todos completado executa para cada registro*!
            eval("d="+data);

            if(d != "erro"){
                var id = d[0];
                var destaque = d[1];
                var midia = d[2];
                var tipo = d[3];

                $.ajax({
                    type: "POST",
                    url: urlBase+"/ajax_midia_thumb.php",
                    cache: false,
                    data: {id: id, midia: midia, destaque: destaque, tipo: tipo},
                    dataType: 'json',
                    beforeSend: function() {
                        //
                    },
                    success: function(data) {
                        $('#enviadas_alvo').append(data.template);
                    },
                    error: function() {
                        alert("Ops, erro ajax...");
                    }
                });
            }

            $('.complete').fadeOut();
        }
    });

    $('#enviando').toggle(function(){
        $('#file_uploadQueue').fadeOut();
    }, function(){
        $('#file_uploadQueue').fadeIn();
    });


    $(document).on('click', '.delete_midia', function(){
        var id = $(this).parent('.galeria_thumb').attr('id');

        var objeto = new Object();
        objeto =  $(this).parent('div');

        $.ajax({
            type: "POST",
            url: urlBase+"/ajax_midia_deleta.php",
            cache: false,
            data: {id: id},
            dataType: 'json',
            context: this,
            beforeSend: function() {
                objeto.html('<p class="msg_delete"><img src="'+diretorio+'/img/loader5.gif">Deletando...</p>');
            },
            success: function(data){
                objeto.fadeOut('slow', function(){
                    $(this).remove();
                    $('#total_imagens').text('Total: '+$('#enviadas_alvo > div').size());
                });
            },
            error: function(){
                alert("Ops, erro ajax...");
            }
        });
    });

    $(document).on('dblclick', '.galeria_thumb > img', function(){
        var id = $(this).parent('.galeria_thumb').attr('id');

        var objeto = new Object();
        objeto = $(this);

        $.ajax({
            type: "POST",
            url: urlBase+"/ajax_midia_destaque.php",
            cache: false,
            data: {id: id},
            beforeSend: function() {
                $('#galeria_form_feedback_sub').show();
            },
            success: function(data) {
                eval("d="+data);
                if(d){
                    $('#enviadas_alvo > div[class=galeria_thumb]').css('border','2px solid #aeaeae');
                    objeto.parent('div').css('border', '2px solid red');
                    $('#galeria_form_feedback_sub').hide();
                }
            },
            error: function() {
                alert("Ops, erro ajax...");
            }
        });
    });

    $(document).on('click', '.link', function(){
        $('.modal-link').show();
    });

    $(document).on('click', '.expand_image', function(){
        $('.modal-midia').show();

        var Midia_ID = $(this).parents(".galeria_thumb").attr("id");

        $.ajax({
            type: "POST",
            url: urlBase+"/ajax_midia_dados.php",
            cache: false,
            data: {Midia_ID: Midia_ID},
            dataType: 'json',
            context: this,
            beforeSend: function() {
                //
            },
            success: function(data){
                if(data){
                    $("input[name='midia_id']").val(data.Midia_ID);
                    $("textarea[name='midia_legenda']").data("wysihtml5").editor.setValue(data.Midia_Legenda);
                    $("input[name='midia_link']").val(data.Midia_Link);
                }
            },
            error: function(){
                alert("Ops, erro ajax...");
            }
        });
    });

    $(document).on('click', '.close', function(){
        $('.modal-link').hide();
        $('.modal-midia').hide();
    });

    $(document).on('click', '.link-gravar', function(){
        var Conteudo_ID = $("#Conteudo_ID").val();
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

        var link_titulo = $("input[name='link_titulo']").val();
        var link_tipo = $("select[name='link_tipo'] option:selected").val();

        $.ajax({
            type: "POST",
            url: urlBase+"/ajax_link.php",
            cache: false,
            data: {Conteudo_ID: Conteudo_ID, link: link.val(), link_titulo: link_titulo, link_tipo: link_tipo},
            dataType: 'json',
            context: this,
            beforeSend: function() {
                //
            },
            success: function(data){
                //window.location.reload(true);
                window.location.href = urlBase+'/conteudo_ie.php?ac=e&id='+Conteudo_ID;
            },
            error: function(){
                alert("Ops, erro ajax...");
            }
        });
    });

    $(document).on('click', '.close-link', function(){
        var id = $(this).attr("id");

        $.ajax({
            type: "POST",
            url: urlBase+"/ajax_link_deleta.php",
            cache: false,
            data: {id: id},
            dataType: 'json',
            context: this,
            beforeSend: function() {
                //
            },
            success: function(data){
                $(this).parents(".box_link").remove();
            },
            error: function(){
                alert("Ops, erro ajax...");
            }
        });
    });

    $(document).on('click', '.midia-gravar', function(){
        var Conteudo_ID = $("#Conteudo_ID").val();
        var Midia_ID = $("input[name='midia_id']").val();
        var legenda = $("textarea[name='midia_legenda']");
        if(legenda.val() == '') {
            legenda.focus();
            legenda.css('border', '1px solid #E33939');
            legenda.css('background', '#FFFFAF');
            return false;
        }else{
            legenda.css('border', '1px solid #d2d6de');
            legenda.css('background', '#FFFFFF');
        }

        var link = $("input[name='midia_link']").val();

        $.ajax({
            type: "POST",
            url: urlBase+"/ajax_midia_dados_gravar.php",
            cache: false,
            data: {Midia_ID: Midia_ID, legenda: legenda.val(), link: link},
            dataType: 'json',
            context: this,
            beforeSend: function() {
                //
            },
            success: function(data){
                window.location.href = urlBase+'/conteudo_ie.php?ac=e&id='+Conteudo_ID;
            },
            error: function(){
                alert("Ops, erro ajax...");
            }
        });
    });
});