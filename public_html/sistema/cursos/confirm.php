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

include($diretorio.'_login.inc.php');


$_REQUEST['link'] = urlSite."cursos/confirm.php";

$smarty->assign('diretorio', $diretorio);
$smarty->assign('urlBase', urlBase);
$smarty->assign('urlSite', urlSite);
$smarty->assign('Info', $Info);



$smarty->assign("dados", $Cursos->Dados(4));
$smarty->display('sistema/cursos/confirm.html');