<?php
$diretorio = "../../";
include($diretorio."_config1.inc.php");

$smarty = new Smarty;
$smarty -> muteExpectedErrors();

$smarty->template_dir = $diretorio.'templates';
$smarty->compile_dir = $diretorio.'templates_c';

$Sessao = new Sessao();
$Conn = new Database();
$Generico = new Generico($Conn);
$Cursos = new Cursos($Conn);

include($diretorio."_login.inc.php");

$pg = 1;
if(isset($_REQUEST['pg'])){
    $pg = $_REQUEST['pg'];
}

$busca = (isset($_REQUEST['b']))?$_REQUEST['b']:null;

$_REQUEST['b'] = $busca;


$smarty->assign('diretorio', $diretorio);
$smarty->assign('urlBase', urlBase);
$smarty->assign('urlSite', urlSite);
$smarty->assign('Mod', $Mod);
$smarty->assign('Busca', $busca);
$smarty->assign('Info', $Info);

$smarty->assign('status', $Cursos->Status(2));
$smarty->assign('dados', $Cursos->Lista($_REQUEST));
$smarty->display('sistema/cursos/cursos.html');