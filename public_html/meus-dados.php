<?php
include("_config.inc.php");
include("gerenciar/libs/Clientes.class.php");

$smarty = new Smarty;
$smarty -> muteExpectedErrors();

$Conn = new Database();
$Sessao = new Sessao();
$Generico = new Generico($Conn);
$Cadastro = new Cadastro($Conn);
$Estados = new Estados($Conn);
$Clientes = new Clientes($Conn);

include("_verifica_login.inc.php");

if($_POST){
    $Cadastro->Edita($_POST);

    header("Location:".urlBase."painel");
    die;
}else{
    //TOKEN
    include("_token.inc.php");
}

$total['not'] = $Generico->Estatisticas("os_fechamentos", "Cliente_ID={$_SESSION['xxx']['login']['Cliente_ID']} AND Fechamento_Status=0");

$smarty->assign("urlBase", urlBase);
$smarty->assign("urlSite", urlSite);
$smarty->assign("nav", $nav);
$smarty->assign('Info', $Info);

$smarty->assign("total", $total);
$smarty->assign('estados', $Estados->Lista());
$smarty->assign("dados", $Generico->DadosClientes($_SESSION['xxx']['login']['Cliente_ID']));
$smarty->display('meus-dados.html');
