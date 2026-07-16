<?php
$diretorio = "../../";
include($diretorio."_config1.inc.php");

$smarty = new Smarty;
$smarty -> muteExpectedErrors();

$smarty->template_dir = $diretorio.'templates';
$smarty->compile_dir = $diretorio.'templates_c';

$Conn = new Database();
$Generico = new Generico($Conn);
$Cursos = new Cursos($Conn);

include($diretorio."_login.inc.php");

$id = 0;
if(isset($_REQUEST['id'])){
	$id = strip_tags(trim($_REQUEST['id']));
}

$smarty->assign('diretorio', $diretorio);
$smarty->assign('urlBase', urlBase);
$smarty->assign('urlSite', urlSite);
$smarty->assign('Mod', $Mod);
$smarty->assign('Info', $Info);
$smarty->assign('id', $id);

$smarty->assign('dados', $Cursos->Dados($id));
$smarty->display('sistema/cursos/sobre-cursos.html');