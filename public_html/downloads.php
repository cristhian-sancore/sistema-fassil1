<?php
include("_config.inc.php");

$smarty = new Smarty;
$smarty -> muteExpectedErrors();

$Conn = new Database();
$Sessao = new Sessao();
$Generico = new Generico($Conn);
$Conteudo = new Conteudo($Conn);

include("_login.inc.php");

$dd = array(
    'Categoria_ID' => 3,
    'pg' => 1,
    'fpg' => 50
);

$smarty->assign("urlBase", urlBase);
$smarty->assign("urlSite", urlSite);
$smarty->assign("nav", $nav);
$smarty->assign('Info', $Info);

$smarty->assign("lista", $Conteudo->Lista($dd));
$smarty->display('downloads.html');
