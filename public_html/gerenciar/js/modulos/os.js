$(document).ready(function() {
    var url = $('#urlModal').val();

    $("[data-mask]").inputmask();

    //Date picker
    $('.datepicker').datepicker({
        autoclose: true,
        format: "dd/mm/yyyy",
        language: "pt-BR"
    });

    $("select[name='p'], select[name='t'], select[name='g'], select[name='s'], select[name='c'], select[name='u'], select[name='n']").change(function(){
        $('.pesquisa').submit();
    });

    $(document).on('click', '.alt_user', function(){
        var cod = $(this).attr('id');

        $.ajax({
            type: "POST",
            url: url+"/ajax_os_usuario.php",
            cache: false,
            data: {cod: cod},
            beforeSend: function() {
                //
            },
            success: function(data){
                location.reload();
            },
            error: function(){
                alert('ops! ocorreu algum erro!');
            }
        });
    });
});