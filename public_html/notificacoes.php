<?php
include("_config.inc.php");
include("gerenciar/libs/OrdemServicos.class.php");

$smarty = new Smarty;
$smarty -> muteExpectedErrors();

$Conn = new Database();
$Sessao = new Sessao();
$Generico = new Generico($Conn);
$Conteudo = new Conteudo($Conn);
$OrdemServicos = new OrdemServicos($Conn);

include("_verifica_login.inc.php");

$get = explode('_', $nav['n2']);
$busca = (isset($get[2]))?$get[2]:'';

$dd = array(
    'Cliente_ID' => $_SESSION['xxx']['login']['Cliente_ID'],
    'pg' => (isset($get[1]))?$get[1]:1,
    'b' => $busca,
    'fpg' => 12,
    'link' => urlBase."painel"
);

$total['not'] = $Generico->Estatisticas("os_fechamentos", "Cliente_ID={$_SESSION['xxx']['login']['Cliente_ID']} AND Fechamento_Status=0");

$smarty->assign("urlBase", urlBase);
$smarty->assign("urlSite", urlSite);
$smarty->assign("nav", $nav);
$smarty->assign('Info', $Info);

$smarty->assign("total", $total);
$smarty->assign("busca", $busca);
$smarty->assign('grupos', $OrdemServicos->Grupos($_SESSION['xxx']['login']['Empresa_ID']));
$smarty->assign("lista", $Generico->ListaFechamentosOS($dd));
$smarty->display('notificacoes.html');
