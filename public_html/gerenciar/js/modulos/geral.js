seletor = 0;

function linkno(evento) {
	evento.preventDefault();
}

function validaTextarea(obj) {
	if(obj.val() == ''){
		obj.data("wysihtml5").editor.focus();
		return true;
	}
}

function validaCampo(obj, cond, email){
	var obj_val = null;
	if(email){
		obj_val = obj.val().indexOf('@');
	}else{
		obj_val = obj.val();
	}

	if(obj_val == cond) {
		obj.focus();
		obj.css('border', '1px solid #E33939');
		obj.css('background', '#FFFFAF');
		return true;
	}else{
		obj.css('border', '1px solid #d2d6de');
		obj.css('background', '#FFFFFF');
	}
}

$(document).ready(function() {
	var url = $('#urlBase').val();

	$('.reg').click(function(){
		var objeto = $(this);
		var url = $("#url").val();

		foobar = $(this).attr('id').split('-');

		var titulo = null;
		var conteudo = null;
		var tipo = null;
		var btn = null;
		if(foobar[0]=='a'){
			titulo = "Confirmação";
			conteudo = "Tem certeza que deseja alterar o Status desse Registro?";
			tipo = "blue";
			btn = "btn-blue";
		}else if(foobar[0]=='d'){
			titulo = "Confirmação";
			conteudo = "Tem certeza que deseja deletar esse Registro? Quando confirmado ele será excluido da Base de Dados!";
			tipo = "red";
			btn = "btn-red";
		}

		$.confirm({
			icon: 'fa fa-warning',
			title: titulo,
			content: conteudo,
			type: tipo,
			columnClass: 'medium',
			typeAnimated: true,
			buttons: {
				confirm:{
					text: 'Confirmar',
					btnClass: btn,
					action: function() {
						$.ajax({
							type: "POST",
							url: url,
							cache: false,
							data: {ac: foobar[0], id: foobar[1]},
							dataType: 'json',
							context: $(this),
							beforeSend: function () {
								//
							},
							success: function (data) {
								if (data.dados['ac'] == 'a') {
									if (data.dados['status'] == 2) {
										objeto.find('span').removeClass().addClass('label label-primary').text('Ativo/Apto');
									} else if (data.dados['status'] == 1) {
										objeto.find('span').removeClass().addClass('label label-success').text('Ativo');
									} else if (data.dados['status'] == 0) {
										objeto.find('span').removeClass().addClass('label label-danger').text('Inativo');
									}
								}

								if (data.dados['ac'] == 'd') {
									objeto.parents('tr').remove();
								}
							},
							error: function () {
								alert('ops! ocorreu algum erro!');
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
	});

	$('#select_all').click(function(){
		alert("teste");
		return false;

		$('.icheckbox_flat-blue').each(function(iteracao, objeto){
			if(seletor%2 == 0) {
				$(this).attr('class').addClass('checked');
			}
			else {
				$(this).attr('checked', false);
			}
		});
		seletor++;
	});

	$('#excluir').click(function(){
		var total = $('.checkbox_cod:checked').length;
		if(total == 0)  {
			alert('Ã‰ necessÃ¡rio escolher pelo menos um UsuÃ¡rio para deletar!')
			return false;
		}
	});

	$(document).on('click', '.menu_bar', function() {
		var caminho = $(this).attr('href');
		var id = $(this).attr("id");

		if (caminho != "#"){
			$.ajax({
				type: "POST",
				url: url + "ajax_menu_bar.php",
				cache: false,
				data: {id: id},
				dataType: 'json',
				beforeSend: function () {
					//
				},
				success: function (data) {
					if (data) {
						window.location.href = caminho;
					}
				},
				error: function () {
					//alert("Ops, erro ajax...");
				}
			});
		}
	});

	$(document).keypress(function(e) {
		$("input[name='b']").focus();
	});
});