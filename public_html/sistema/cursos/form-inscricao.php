<?php
$diretorio = "../../";
include($diretorio.'_config1.inc.php');


$smarty = new Smarty;
$smarty -> muteExpectedErrors();

$smarty->template_dir = $diretorio.'templates';
$smarty->compile_dir = $diretorio.'templates_c';

$Conn = new Database();
$Generico = new Generico($Conn);
$Cursos = new Cursos($Conn);

$ac = strip_tags(trim($_REQUEST['ac']));
$id = 0;
if(isset($_REQUEST['id'])){
	$id = strip_tags(trim($_REQUEST['id']));
}

if($_POST){
	if($ac=='i'){
	   // echo"Estou aqui";
		$Cursos->Grava($_POST, $id);
	}
    header("Location:".urlSite."cursos/confirm.php");
	
}
// include("_login.inc.php");
$_REQUEST['link'] = urlSite."cursos/form-inscricao.php";

$smarty->assign('diretorio', $diretorio);
$smarty->assign('urlBase', urlBase);
$smarty->assign('urlSite', urlSite);
$smarty->assign('Info', $Info);
// $smarty->assign('ajax', $ajax);


$smarty->assign('dados', $Cursos->Dados($_REQUEST));
$smarty->assign('cidades', $Cursos->Cidades());
$smarty->assign('status', $Cursos->Status(2));
$smarty->display('sistema/cursos/form-inscricao.html');
