Msg_ID = null;

function EnviaAnexo(){
    var os = $('#os').val();

    $('#anexo-ticket').data('uploadifive').settings.formData = { 'id'  : os};
    $('#anexo-ticket').uploadifive('upload');
}

function EnviaMensagem() {
    var urlBase = $('#urlBase').val();

    var descricao = CKEDITOR.instances.editor;
    if(descricao.getData() == '') {
        descricao.focus();
        return false;
    }

    $('.enviando').fadeIn();

    $.ajax({
        type: "POST",
        url: urlBase + '/ajax_envia_mensagem.php',
        cache: false,
        async: false,
        data: {os: $('#os').val(), conteudo: descricao.getData()},
        dataType: 'json',
        beforeSend: function() {
            //
        },
        success: function(data){
            Msg_ID = data.id;
            descricao.setData('');
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
    var urlBase = $('#urlBase').val();
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
                $("#chat_box").empty();
                $('#os_qtd').val(data.total);
                $("#chat_box").append(data.template);
                $("#chat_box").stop().animate({ scrollTop: $("#chat_box")[0].scrollHeight}, 1000);
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
    var urlBase = $('#urlBase').val();

    initSample();

    /*CONFIRMACAO*/
    $(document).on('click', '.btn_confirmar', function(){
        $.confirm({
            boxWidth: '40%',
            useBootstrap: false,
            icon: 'fa fa-warning',
            title: 'Confirmação',
            content: 'Tem certeza que deseja <b>CONFIRMAR</b> o fechamento desse <b>TICKET</b>?',
            type: 'red',
            typeAnimated: true,
            buttons: {
                confirm:{
                    text: 'Confirmar',
                    btnClass: 'btn-red',
                    action: function() {
                        $('.fechamento').submit();
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
    $("#chat_box").stop().animate({ scrollTop: $("#chat_box")[0].scrollHeight}, 1000);
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

    /*MENSAGEM ANEXO*/
    $('#anexo').uploadifive({
        'buttonClass'  : 'btn btn-anexo',
        'buttonText'   : "<i class='fa fa-paperclip'></i>",
        'width'        : 37,
        'height'       : 35,
        'auto'         : false,
        'uploadScript' : urlBase + '/ajax_envia_mensagem.php',
        'onUploadComplete' : function(file, data) {
            $('.complete').fadeOut();
        }
    });
    /*FIM ANEXO*/

    /*ANEXO DOCUMENTOS*/
    $('#anexo-ticket').uploadifive({
        'buttonClass'  : 'btn-anexo-ticket',
        'buttonText'   : "<i class='fa fa-paperclip' style='padding: 10px 0 0 0px; color: #FFFFFF;'></i>",
        'width'        : 37,
        'height'       : 35,
        'auto'         : false,
        'uploadScript' : urlBase + '/ajax_ticket_anexo.php',
        'onUploadComplete' : function(file, data) {
            eval("d="+data);

            if(d != "erro"){
                var id = d[0];
                var caminho = d[1];
                var nome = d[2];

                $.ajax({
                    type: "POST",
                    url: urlBase+"/ajax_ticket_anexo_thumb.php",
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
            url: urlBase+"/ajax_ticket_anexo_excluir.php",
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
    /*FIM ANEXO DOCUMENTOS*/

    $(document).on('click', '.btn_avaliar', function(){
        var id = $(this).attr('id');
        var nota = $('input[name=av_nota]:checked').val();
        var comentario = $('textarea[name=av_comentario]').val();

        if(!nota){
            alert('Clique na ESTRELA para poder Avaliar o Atendimento.');
            return false;
        }

        $.confirm({
            boxWidth: '40%',
            useBootstrap: false,
            icon: 'fa fa-warning',
            title: 'Confirmação',
            content: 'Depois de <b>CONFIRMADA</b> não podera ser alterada.',
            type: 'red',
            typeAnimated: true,
            buttons: {
                confirm:{
                    text: 'Confirmar',
                    btnClass: 'btn-red',
                    action: function() {
                        $.ajax({
                            type: "POST",
                            url: urlBase+"/ajax_avalia_atendimento.php",
                            cache: false,
                            data: {id: id, nota: nota, comentario: comentario},
                            dataType: 'json',
                            context: this,
                            beforeSend: function() {
                                //
                            },
                            success: function(data){
                                window.location.reload();
                            },
                            error: function(){
                                console.debug("Ops, erro ajax...");
                            }
                        });
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
});