<?php
$diretorio = "../../";
include($diretorio."_config.inc.php");

$smarty = new Smarty;
$smarty -> muteExpectedErrors();

$smarty->template_dir = $diretorio.'templates';
$smarty->compile_dir = $diretorio.'templates_c';
$smarty->cache_dir = $diretorio.'cache';

$Sessao = new Sessao();
$Conn = new Database();
$Menu = new Menu($Conn);
$Entidade = new Entidade($Conn);

$ajax = "ajax_entidades_acao.php";

if(!isset($_SESSION['_user'])){
    header("Location:".urlBase."index.php");
}

$pg = 1;
if(isset($_REQUEST['pg'])){
    $pg = $_REQUEST['pg'];
}
$busca = null;
if(isset($_REQUEST['b'])){
    $busca = $_REQUEST['b'];
}
$_REQUEST['pg'] = $pg;
$_REQUEST['b'] = $busca;
$_REQUEST['link'] = urlBase."$Mod[0]/$Mod[1]/entidades.php";

$smarty->assign('diretorio', $diretorio);
$smarty->assign('urlBase', urlBase);
$smarty->assign('Mod', $Mod);
$smarty->assign('MenuBar', $Menu->MenuBar());
$smarty->assign('Info', $Info);
$smarty->assign('ajax', $ajax);

$smarty->assign('lista', $Entidade->Lista($_REQUEST));
$smarty->display('website/entidades/entidades.html');