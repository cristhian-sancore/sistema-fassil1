$(document).ready(function () {
    var url = $('#url').val();

    $('#gravar').click(function () {
        if (validaCampo($("input[name='nome']"), '', false)) return false;
    });

    $('.add_orgao').click(function () {
        var qtd = $('.qtd_orgao');
        qtd = qtd.length?qtd.length:0;

        var dd = '<div class="row qtd_orgao">\n' +
            '                                        <div class="col-xs-4">\n' +
            '                                            <div class="form-group">\n' +
            '                                                <label>Nome</label>\n' +
            '                                                <input type="text" class="form-control" name="orgao['+qtd+'][]">\n' +
            '                                            </div>\n' +
            '                                        </div>\n' +
            '\n' +
            '                                        <div class="col-xs-4">\n' +
            '                                            <div class="form-group">\n' +
            '                                                <label>Link</label>\n' +
            '                                                <input type="text" class="form-control" name="link['+qtd+'][]">\n' +
            '                                            </div>\n' +
            '                                        </div>\n' +
            '\n' +
            '                                        <div class="col-xs-3">\n' +
            '                                            <label>&nbsp;</label>\n' +
            '                                            <div class="form-group">\n' +
            '                                                <label style="margin-right: 20px;">\n' +
            '                                                    <input type="checkbox" name="orgao_sis['+qtd+'][]"> Sistema\n' +
            '                                                </label>\n' +
            '                                                <label>\n' +
            '                                                    <input type="checkbox" name="orgao_con['+qtd+'][]"> Consultoria\n' +
            '                                                </label>\n' +
            '                                            </div>\n' +
            '                                        </div>\n' +
            '\n' +
            '                                        <div class="col-xs-1">\n' +
            '                                            <label>&nbsp;</label>\n' +
            '                                            <button type="button" class="btn btn-block btn-danger rmv_orgao"><i class="fa fa-times"></i></button>\n' +
            '                                        </div>\n' +
            '                                    </div>';

        $('.box_orgaos').append(dd);
    });

    $(".rmv_orgao").on( "click", function() {
        $(this).parents('.qtd_orgao').remove();
    });
});