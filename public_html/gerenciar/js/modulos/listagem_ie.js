function formataMoeda(num) {
    x = 0;

    if(num < 0) {
        num = Math.abs(num);
        x = 1;
    }

    if(isNaN(num)) {
        num = "0";
    }

    cents = Math.floor((num*100+0.5)%100);

    num = Math.floor((num*100+0.5)/100).toString();

    if(cents < 10) {
        cents = "0" + cents;
    }

    for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++) {
        num = num.substring(0,num.length-(4*i+3))+'.'+num.substring(num.length-(4*i+3));
    }

    ret = num + ',' + cents;

    if (x == 1) {
        ret = ' - ' + ret;
    }

    return ret;
}

function desformataMoeda(num) {
    var subvalor = num.replace(/\./g,'');
    var valor = parseFloat(subvalor.replace(',','.'));
    return valor;
}

function calculo(valor, desconto) {
    var valor = desformataMoeda(valor);
    var percentual = desconto / 100;
    var valor_desconto = valor - (percentual * valor);

    return formataMoeda(valor_desconto);
}

$(document).ready(function() {
    var url = $('#url').val();

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

    $("input[name='quantidade'], input[name='desconto']").bind('keyup', function(e) {
        var qtd = parseInt($(this).val());
        if(e.keyCode==38){
            if(qtd == 0){
                $i = 1;
            }else{
                $i = qtd;
            }
            $(this).val($i+1);
        }else if(e.keyCode==40){
            if(qtd == 1){
                $i = 2;
            }else{
                $i = qtd;
            }
            $(this).val($i-1);
        }
    });

    $("input[name='desconto']").bind("blur focus", function(e) {
        e.preventDefault();

        var expre = /[^0-9]/g;
        // REMOVE OS CARACTERES DA EXPRESSAO ACIMA
        if ($(this).val().match(expre)) {
            $(this).val($(this).val().replace(expre, ''));
        }

        if($(this).val() == '' || $(this).val() < 5){
            $(this).val(5);
        }else if($(this).val() > 99){
            $(this).val(99);
        }

        valor = $("input[name='valor']").val();
        desconto = $("input[name='desconto']").val();
        $("input[name='valor_final']").val(calculo(valor, desconto));
    });

    /* Mascaras Moeda */
    $("input[name='valor'], input[name='valor_final']").maskMoney({
        decimal:",",
        thousands:".",
        allowZero: false
    });

    $("input[name='valor']").bind("keyup blur focus", function(e) {
        valor = $("input[name='valor']").val();
        desconto = $("input[name='desconto']").val();
        $("input[name='valor_final']").val(calculo(valor, desconto));
    });

    $("select[name='categoria']").change(function(){
        var cod = $(this).val();
        var subcat = $('#subcat').val();

        $.ajax({
            type: "POST",
            url: url+"/ajax_anuncio_subcategoria.php",
            cache: false,
            data: {cod: cod, subcat: subcat},
            beforeSend: function() {
                //
            },
            success: function(data){
                $(".box_subcat").fadeIn();
                $("select[name='subcategoria']").html(data);
            },
            error: function(){
                alert('ops! ocorreu algum erro!');
            }
        });
    });

    if($("select[name='categoria'] option:selected").val() != 0){
        $("select[name='categoria']").trigger( "change" );
    }

    $("select[name='formaPgto']").change(function(){
        if($(this).val() == 1){
            $(".box_pagamento").fadeIn();
        }else{
            $(".box_pagamento").fadeOut();
        }
    });

    if($("select[name='formaPgto'] option:selected").val() == 1){
        $("select[name='formaPgto']").trigger( "change" );
    }

    $(document).on('click', '.deleta_img', function(){
        var id = $(this).attr('id');

        $.ajax({
            type: "POST",
            url: url+"/ajax_anuncio_deleta_imagem.php",
            cache: false,
            data: {id: id},
            dataType: 'json',
            context: this,
            beforeSend: function() {
                //
            },
            success: function(data){
                window.location.reload();
            },
            error: function(){
                alert('ops! ocorreu algum erro!');
            }
        });
    });

    $(document).on('click', '.destaque_img', function(){
        var id = $(this).attr('id');

        $.ajax({
            type: "POST",
            url: url+"/ajax_anuncio_destaque_imagem.php",
            cache: false,
            data: {id: id},
            dataType: 'json',
            context: this,
            beforeSend: function() {
                //
            },
            success: function(data){
                window.location.reload();
            },
            error: function(){
                alert('ops! ocorreu algum erro!');
            }
        });
    });
});