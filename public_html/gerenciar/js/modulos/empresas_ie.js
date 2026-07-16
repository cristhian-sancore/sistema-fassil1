$(document).ready(function() {
    $("[data-mask]").inputmask();

    var url = $('#url').val();

    $('#gravar').click(function(){
        if(validaCampo($("input[name='nome']"), '', false)) return false;
        if(validaCampo($("input[name='endereco']"), '', false)) return false;
        if(validaCampo($("input[name='numero']"), '', false)) return false;
        if(validaCampo($("input[name='bairro']"), '', false)) return false;
        if(validaCampo($("input[name='contato1']"), '', false)) return false;
    });

    $(document).on('change', '.estado', function() {
        var cod = $(this).val();
        var subcat = $('#subcidade').val();

        $.ajax({
            type: "POST",
            url: url+"/ajax_cidades.php",
            cache: false,
            data: {cod: cod, subcat: subcat},
            context: $(this),
            beforeSend: function() {
                //
            },
            success: function(data){
                //$(".box_subcat").fadeIn();
                $(this).parents('.cx_ce').find(".cidade").html(data);
            },
            error: function(){
                alert('ops! ocorreu algum erro!');
            }
        });
    });

    $('.add_ec').click(function(){
        var cod = $(this).val();
        var subcat = $('#subcidade').val();

        $.ajax({
            type: "POST",
            url: url+"/ajax_ec.php",
            cache: false,
            data: {cod: cod, subcat: subcat},
            context: $(this),
            beforeSend: function() {
                //
            },
            success: function(data){
                $('.box_ce').append(data);
            },
            error: function(){
                alert('ops! ocorreu algum erro!');
            }
        });
    });

    $(document).on('click', '.del_ec', function() {
        var emp = $(this).attr('id').split('-')[0];
        var ec = $(this).attr('id').split('-')[1];

        $.ajax({
            type: "POST",
            url: url+"/ajax_ec_deleta.php",
            cache: false,
            data: {emp: emp, ec: ec},
            context: $(this),
            beforeSend: function() {
                //
            },
            success: function(data){
                //$(".box_subcat").fadeIn();
                $(this).parents('.cx_ce').remove();
            },
            error: function(){
                alert('ops! ocorreu algum erro!');
            }
        });
    });
});