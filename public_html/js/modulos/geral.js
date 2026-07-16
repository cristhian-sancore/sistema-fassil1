/*function Lazyload(){
	$("img.lazy").lazyload({
	effect : "fadeIn"
	});
}*/

function validaEmail(email) {
    if(email) {
        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
        return emailReg.test(email);
    }else{
        return false;
    }
}

function verificaPass(pass){
    var strongRegex = new RegExp("^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})");
    var mediumRegex = new RegExp("^(((?=.*[a-z])(?=.*[A-Z]))|((?=.*[a-z])(?=.*[0-9]))|((?=.*[A-Z])(?=.*[0-9])))(?=.{6,})");
    return(mediumRegex.test(pass));

}

function validaCampo(obj, cond, email){
    var obj_val = null;
    if(email){
        obj_val = validaEmail(obj.val());
    }else{
        obj_val = obj.val();
    }

    if(obj_val == cond) {
        obj.focus();
        obj.css('border', '1px solid #E33939');
        obj.css('background', '#FFFFAF');
        return true;
    }else{
        obj.css('border', '1px solid #cccccc');
        obj.css('background', '#F2F2F2');
    }
}

$(document).ready(function() {
    var urlBase = $('#url').val();
    //Lazyload();

    $('#logar').click(function () {
        if (validaCampo($("input[name='c_login']"), '', false)) return false;
        if (validaCampo($("input[name='c_senha']"), '', false)) return false;
    });

    //CONTROLE DO MENU MOBILE
    $('.mobile_action').click(function () {
        if (!$(this).hasClass('active')) {
            $(this).addClass('active');
            $('.nav_main').animate({'left': '0px'}, 300);
        } else {
            $(this).removeClass('active');
            $('.nav_main').animate({'left': '-100%'}, 300);
        }
    });

    /*ANIMAZE*/
    $('.animaze').bind('inview', function(event, visible) {
        if (visible) {
            $(this).stop().animate({
                opacity: 1,
                top: '0px',
                left: '0px'
            }, 800);
        }
    });

    $('.animaze').stop().animate({
        opacity: 0
    });
    /*FIM ANIMAZE*/

    $(".data").mask('99/99/9999');
    $(".horario").mask('99:99');
    $(".fone").mask('(99) 9999-9999');
    $(".cel").mask('(99) 9 9999-9999');

    $('.aniimated-thumbnials').lightGallery({
        thumbnail:false,
        animateThumb: false,
        zoom:true
    });

    $(window).scroll(function(){
        if ($(this).scrollTop() > 100) {
            $('a[href="#top"]').fadeIn();
        } else {
            $('a[href="#top"]').fadeOut();
        }
    });

    $('a[href="#top"]').click(function(){
        $('html, body').animate({scrollTop : 0},800);
        return false;
    });

    $(document).on('click', '.scroll', function(event) {
        event.preventDefault();
        $('html,body').animate({scrollTop:$(this.hash).offset().top}, 800);
    });

    //SLIDER
    var action = setInterval(slideGo, 10000);

    $('.slide_nav.go').click(function () {
        clearInterval(action);
        slideGo();
    });

    $('.slide_nav.back').click(function () {
        clearInterval(action);
        slideBack();
    });

    function slideGo() {
        if ($('.slide_item.first').next().size()) {
            $('.slide_item.first').fadeOut(400, function () {
                $(this).removeClass('first').next().fadeIn().addClass('first');
            });
        } else {
            $('.slide_item.first').fadeOut(400, function () {
                $('.slide_item').removeClass('first');
                $('.slide_item:eq(0)').fadeIn().addClass('first');
            });
        }
    }

    function slideBack() {
        if ($('.slide_item.first').index() > $('.slide_item').length) {
            $('.slide_item.first').fadeOut(400, function () {
                $(this).removeClass('first').prev().fadeIn().addClass('first');
            });
        } else {
            $('.slide_item.first').fadeOut(400, function () {
                $('.slide_item').removeClass('first');
                $('.slide_item:last-of-type').fadeIn().addClass('first');
            });
        }
    }

    $('.ajax_reponse_close').click(function(){
        $(this).parents('.ajax_reponse').fadeOut();
    });
});