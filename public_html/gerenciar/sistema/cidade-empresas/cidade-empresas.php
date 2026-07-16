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
$CidadeEmpresas = new CidadeEmpresas($Conn);

include($diretorio."_config_permissao.inc.php");

$ajax = "ajax_cidade-empresas_acao.php";

$pg = 1;
if(isset($_REQUEST['pg'])){
    $pg = $_REQUEST['pg'];
}

$busca = (isset($_REQUEST['b']))?$_REQUEST['b']:null;
$cidade = (isset($_REQUEST['c']))?$_REQUEST['c']:null;
$status = (isset($_REQUEST['s']))?$_REQUEST['s']:null;

$_REQUEST['pg'] = $pg;
$_REQUEST['b'] = $busca;
$_REQUEST['s'] = $status;
$_REQUEST['link'] = urlBase."$Mod[0]/$Mod[1]/cidade-empresas.php";

$smarty->assign('diretorio', $diretorio);
$smarty->assign('urlBase', urlBase);
$smarty->assign('Mod', $Mod);
$smarty->assign('MenuBar', $Menu->MenuBar());
$smarty->assign('Info', $Info);
$smarty->assign('ajax', $ajax);

$smarty->assign('Status', $status);
$smarty->assign('lista', $CidadeEmpresas->Lista($_REQUEST));
$smarty->display('sistema/cidade-empresas/cidade-empresas.html');