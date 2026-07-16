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
$Menu = new Menu($Conn);
$Inscritos = new Inscritos($Conn);

include($diretorio . "_config_permissao.inc.php");

$ajax = "ajax_inscritos_acao.php";

$pg = 1;
if(isset($_REQUEST['pg'])){
    $pg = $_REQUEST['pg'];
}

$busca = (isset($_REQUEST['b']))?$_REQUEST['b']:null;
$nome = (isset($_REQUEST['n']))?$_REQUEST['n']:null;
$codigo = (isset($_REQUEST['c']))?$_REQUEST['c']:null;
$status = (isset($_REQUEST['s']))?$_REQUEST['s']:null;
$entidade = (isset($_REQUEST['e']))?$_REQUEST['e']:null;
$cidade = (isset($_REQUEST['cid']))?$_REQUEST['cid']:null;

$_REQUEST['pg'] = $pg;
$_REQUEST['n'] = $nome;
$_REQUEST['b'] = $busca;
$_REQUEST['c'] = $codigo;
$_REQUEST['s'] = $status;
$_REQUEST['e'] = $entidade;
$_REQUEST['cid'] = $cidade;
$_REQUEST['link'] = urlBase."$Mod[0]/$Mod[1]/inscritos.php";

$smarty->assign('diretorio', $diretorio);
$smarty->assign('urlBase', urlBase);
$smarty->assign('Mod', $Mod);
$smarty->assign('MenuBar', $Menu->MenuBar());
$smarty->assign('Info', $Info);
$smarty->assign('Busca', $busca);
$smarty->assign('Nome', $nome);
$smarty->assign('Codigo', $codigo);
$smarty->assign('Status', $status);
$smarty->assign('Entidade', $entidade);
$smarty->assign('Cidade', $cidade);
$smarty->assign('ajax', $ajax);

$smarty->assign('Cursos', $Inscritos->Cursos());
$smarty->assign('Cidades', $Inscritos->Cidades());
$smarty->assign('lista', $Inscritos->Lista($_REQUEST));
$smarty->display('sistema/inscritos/inscritos.html');