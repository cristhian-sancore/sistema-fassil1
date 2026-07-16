Msg_ID = null;

function EnviaAnexo(){
    var os = $('#os').val();

    $('#os_upload').data('uploadifive').settings.formData = { 'id'  : os};
    $('#os_upload').uploadifive('upload');
}

function EnviaMensagem() {
    var urlBase = $('#url').val();

    if(validaTextarea($("#conteudo"))) return false;
    $('.enviando').fadeIn();

    $.ajax({
        type: "POST",
        url: urlBase + '/ajax_envia_mensagem.php',
        cache: false,
        async: false,
        data: {os: $('#os').val(), conteudo: $('#conteudo').val()},
        dataType: 'json',
        beforeSend: function() {
            //
        },
        success: function(data){
            Msg_ID = data.id;
            $('#conteudo').data("wysihtml5").editor.clear();
            //verificaMensagem();
            Mensagem();
        },
        error: function(){
            alert("Ops, erro ajax...");
        }
    });

    $('#anexo').data('uploadifive').settings.formData = { 'id'  : Msg_ID};
    $('#anexo').uploadifive('upload');
}

function Mensagem() {
    var urlBase = $('#url').val();
    var os = $('#os').val();
    var os_qtd = $('#os_qtd').val();

    $.ajax({
        type: "POST",
        url: urlBase+"/ajax_verifica_mensagem.php",
        cache: false,
        data: {os: os, os_qtd: os_qtd},
        dataType: 'json',
        beforeSend: function() {
            //
        },
        success: function(data){
            if(data) {
                /*dispara som*/
                if(data.toca){
                    ion.sound.play("bell_ring");
                }

                $('.enviando').fadeOut();
                $("#chat-box").empty();
                $('#os_qtd').val(data.total);
                $("#chat-box").append(data.template);
                $("#chat-box").stop().animate({ scrollTop: $("#chat-box")[0].scrollHeight}, 1000);
            }
        },
        error: function(){
            console.debug("Ops, erro ajax...");
        }
    });
}

var receptor = null;
function verificaMensagem(){
    receptor = setInterval(Mensagem, 30000); //300000 MS == 5 minutes
}

function stopMensagem(){
    clearInterval(receptor);
}

$(document).ready(function() {
    var url = $('#url').val();
    var urlBase = $('#urlBase').val();

    /*VALIDA CAMPOS*/
    $('#gravar').click(function(){
        if(validaCampo($("input[name='cliente']"), '', false)) return false;
        if(validaCampo($("input[name='cliente_id']"), '', false)) return false;
        if(validaCampo($("input[name='data_inicio']"), '', false)) return false;
        if(validaCampo($("input[name='horas_inicio']"), '', false)) return false;
        if(validaCampo($("select[name='equipamento']"), '0', false)) return false;
        if(validaCampo($("select[name='prioridade']"), '0', false)) return false;
        if(validaCampo($("textarea[name='descricao']"), '', false)) return false;
    });
    /*FIM VALIDA CAMPOS*/

    /*CONFIRMACAO*/
    $(document).on('click', '.btn_fechar_os', function(){
        var location = $(this).attr('formaction');

        $.confirm({
            icon: 'fa fa-warning',
            title: 'Confirmação',
            content: 'Tem certeza que deseja <b>FECHAR</b> essa Ordem de Serviço?',
            type: 'red',
            columnClass: 'medium',
            typeAnimated: true,
            buttons: {
                confirm:{
                    text: 'Confirmar',
                    btnClass: 'btn-red',
                    action: function() {
                        $('.form').attr('action', location);
                        $('.form').submit();
                    }
                },
                cancel:{
                    text: 'Cancelar',
                    action: function() {
                        //
                    }
                }
            }
        });
        return false;
    });
    /*FIM CONFIRMACAO*/

    /*SCROOL MENSAGEM*/
    $('#chat-box').slimScroll({ scrollTo: '100000px' });
    if($('#status').val() != '3') {
        verificaMensagem();
    }
    /*FIM SCROOL MENSAGEM*/

    /*SOM MENSAGEM*/
    ion.sound({
        sounds: [
            {name: "door_bell"},
            {name: "bell_ring"},
        ],

        // main config
        path: urlBase+"sounds/",
        preload: true,
        multiplay: true,
        volume: 1.0
    });
    /*FIM SOM MENSAGEM*/

    /*SELECIONA ANEXO*/
    var $input    = document.getElementById('anexo');

    $input.addEventListener('change', function(){
        var name = this.value.split("fakepath\\");
        $('#anexo_nome').html("<i class='fa fa-cloud-upload'></i> "+name[1]);
    });
    /*FIM SELECIONA ANEXO*/

    /*OS ANEXO*/
    $('#os_upload').uploadifive({
        'buttonClass'  : 'btn btn-primary',
        'buttonText'   : "<i class='fa fa-paperclip'></i>",
        'width'        : 37,
        'height'       : 34,
        'auto'         : false,
        'uploadScript' : url + '/ajax_os_upload.php',
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
    /*FIM ANEXO*/

    /*MENSAGEM ANEXO*/
    $('#anexo').uploadifive({
        'buttonClass'  : 'btn btn-primary',
        'buttonText'   : "<i class='fa fa-paperclip'></i>",
        'width'        : 37,
        'height'       : 34,
        'auto'         : false,
        'uploadScript' : url + '/ajax_envia_mensagem.php',
        'onUploadComplete' : function(file, data) {
            $('.complete').fadeOut();
        }
    });
    /*FIM ANEXO*/
});