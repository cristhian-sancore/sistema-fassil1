$(document).ready(function() {
	$('#logar').click(function(){
		var url = $('#url').val();

		if (validaCampo($("input[name='login']"), '', false)) return false;
		if (validaCampo($("input[name='senha']"), '', false)) return false;

		var captcha = grecaptcha.getResponse();
		if(captcha == '') {
			$(".error_captcha").html("Marque o recaptcha.");
			return false;
		}else{
			$(".error_captcha").html("");
		}

		$.ajax({
			type: "POST",
			url: url+"ajax_verifica_recaptcha.php",
			cache: false,
			data: {captcha: grecaptcha.getResponse()},
			context: this,
			dataType: 'json',
			beforeSend: function() {
				//
			},
			success: function(data){
				if(data.permissao){
					$(".error_captcha").text("");
					$('.form_').submit();
				}else{
					$(".error_captcha").html("Erro inesperado! Atualiza a pagina.");
					return false;
				}
			},
			error: function(){
				console.debug('ops! ocorreu algum erro!');
			}
		});
		return false;
	});

	$('.ajax_reponse_close').click(function(){
		$(this).parents('.ajax_reponse').fadeOut();
	});
});
