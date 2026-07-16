<?php
$diretorio = "../../";
include($diretorio . "_config.inc.php");

$smarty = new Smarty;
$smarty -> muteExpectedErrors();

$smarty->template_dir = $diretorio.'templates';
$smarty->compile_dir = $diretorio.'templates_c';
$smarty->cache_dir = $diretorio.'cache';

$Sessao = new Sessao();
$Conn = new Database();
$Clientes = new Clientes($Conn);

if(!isset($_SESSION['_user'])){
	header("Location:".urlBase."index.php");
}

if($_POST) {
	if($_POST['ac']=='d'){
		$Clientes->Deleta($_POST['id']);
	}elseif($_POST['ac']=='a'){
		$_POST['status'] = $Clientes->Ativar($_POST['id']);
	}

	echo json_encode(array('dados' => $_POST));
}