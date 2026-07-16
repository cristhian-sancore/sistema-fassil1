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
$OrdemServicos = new OrdemServicos($Conn);

include($diretorio."_config_permissao.inc.php");

$ajax = "ajax_os_acao.php";

$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$parametros = explode('?', $url);
if(isset($parametros[1])) {
    $parametros = $parametros[1];
}else{
    $parametros = null;
}

$pg = (isset($_REQUEST['pg']))?$_REQUEST['pg']:1;
$ac = (isset($_REQUEST['ac']))?$_REQUEST['ac']:null;

$busca = (isset($_REQUEST['b']))?$_REQUEST['b']:null;
$status = (isset($_REQUEST['s']))?$_REQUEST['s']:null;
$tipo = (isset($_REQUEST['t']))?$_REQUEST['t']:null;
$grupo = (isset($_REQUEST['g']))?$_REQUEST['g']:null;
$usuario = (isset($_REQUEST['u']))?$_REQUEST['u']:null;
$prioridade = (isset($_REQUEST['p']))?$_REQUEST['p']:null;
$data1 = (isset($_REQUEST['d1']))?$_REQUEST['d1']:null;
$data2 = (isset($_REQUEST['d2']))?$_REQUEST['d2']:null;
$cidade = (isset($_REQUEST['c']))?$_REQUEST['c']:null;
$notificacao = (isset($_REQUEST['n']))?$_REQUEST['n']:null;

$_REQUEST['pg'] = $pg;
$_REQUEST['b'] = $busca;
$_REQUEST['s'] = $status;
$_REQUEST['t'] = $tipo;
$_REQUEST['u'] = $usuario;
$_REQUEST['g'] = $grupo;
$_REQUEST['p'] = $prioridade;
$_REQUEST['d1'] = $data1;
$_REQUEST['d2'] = $data2;
$_REQUEST['c'] = $cidade;
$_REQUEST['n'] = $notificacao;
$_REQUEST['link'] = urlBase."$Mod[0]/$Mod[1]/os.php";

$smarty->assign('diretorio', $diretorio);
$smarty->assign('urlBase', urlBase);
$smarty->assign('Mod', $Mod);
$smarty->assign('MenuBar', $Menu->MenuBar());
$smarty->assign('Info', $Info);
$smarty->assign('ajax', $ajax);

$smarty->assign('ac', $ac);
$smarty->assign('Status', $status);
$smarty->assign('Tipo', $tipo);
$smarty->assign('Prioridade', $prioridade);
$smarty->assign('Grupo', $grupo);
$smarty->assign('Tipo', $tipo);
$smarty->assign('Usuario', $usuario);
$smarty->assign('Cidade', $cidade);
$smarty->assign('Data1', $data1);
$smarty->assign('Data2', $data2);
$smarty->assign('Notificacao', $notificacao);
$smarty->assign('Busca', $busca);
$smarty->assign('Params', $parametros);
$smarty->assign('prioridades', $OrdemServicos->Prioridades($_SESSION['_user']['Empresa_ID']));
$smarty->assign('grupos', $OrdemServicos->Grupos($_SESSION['_user']['Empresa_ID']));
$smarty->assign('tipos', $OrdemServicos->Tipos($_SESSION['_user']['Empresa_ID']));
$smarty->assign('status', $OrdemServicos->Status());
$smarty->assign('usuarios', $OrdemServicos->Usuarios());
$smarty->assign('cidades', $OrdemServicos->Cidades());
$smarty->assign('lista', $OrdemServicos->Lista($_REQUEST));
$smarty->display('sistema/os/os.html');
