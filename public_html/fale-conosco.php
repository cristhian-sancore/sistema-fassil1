<?php
include("_config.inc.php");

$smarty = new Smarty;
$smarty -> muteExpectedErrors();

$Conn = new Database();
$Sessao = new Sessao();
$Generico = new Generico($Conn);
$Email = new Email($Conn);

include("_login.inc.php");

/*ENVIA EMAIL*/
if($_POST && $_SERVER['SERVER_NAME']!='localhost'){
    $_POST['url'] = urlBase;
    $smarty->assign('dados', $_POST);
    $template = $smarty->fetch('_email_contato.html');

    $status_envio = $Email->Contato($_POST, $template);
    if($status_envio){
        header("Location:".urlBase."fale-conosco");
        exit;
    }
}

$smarty->assign("urlBase", urlBase);
$smarty->assign("urlSite", urlSite);
$smarty->assign("nav", $nav);
$smarty->assign('Info', $Info);

$smarty->display('fale-conosco.html');
