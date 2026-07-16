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
$Conteudo = new Conteudo($Conn);

include($diretorio."_config_permissao.inc.php");

$ajax = "ajax_conteudo_acao.php";

$pg = 1;
if(isset($_REQUEST['pg'])){
    $pg = $_REQUEST['pg'];
}
$busca = (isset($_REQUEST['b']))?$_REQUEST['b']:null;

$_REQUEST['pg'] = $pg;
$_REQUEST['b'] = $busca;
$_REQUEST['link'] = urlBase."$Mod[0]/$Mod[1]/conteudo.php";

$smarty->assign('diretorio', $diretorio);
$smarty->assign('urlBase', urlBase);
$smarty->assign('Mod', $Mod);
$smarty->assign('MenuBar', $Menu->MenuBar());
$smarty->assign('Info', $Info);
$smarty->assign('ajax', $ajax);

$smarty->assign('lista', $Conteudo->Lista($_REQUEST));
$smarty->display('website/conteudo/conteudo.html');