<?php
include("_config.inc.php");

$smarty = new Smarty;
$smarty -> muteExpectedErrors();

$Conn = new Database();
$Sessao = new Sessao();
$Generico = new Generico($Conn);
$Email = new Email($Conn);
$Estados = new Estados($Conn);
$Cadastro = new Cadastro($Conn);

include("_login.inc.php");

if($_POST){
    $Cliente_ID = $Cadastro->Grava($_POST);

    /*ENVIA EMAIL*/
    if($Cliente_ID && $_SERVER['SERVER_NAME']!='localhost') {
        $dd = $Cadastro->Dados($Cliente_ID);

        $dd['url'] = urlBase;
        $dd['senha'] = $_POST['senha'];
        $smarty->assign('dados', $dd);
        $template = $smarty->fetch('_email_cadastro.html');

        $Email->Cadastro($_POST, $template);
    }

    header("Location:".urlBase."cadastro_ok");
    die;
}else{
    //TOKEN
    include("_token.inc.php");
}

$smarty->assign("urlBase", urlBase);
$smarty->assign("urlSite", urlSite);
$smarty->assign("nav", $nav);
$smarty->assign('Info', $Info);

$smarty->assign('estados', $Estados->Lista());
$smarty->display('cadastro.html');
