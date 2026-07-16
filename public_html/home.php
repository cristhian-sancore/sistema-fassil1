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
    'Categoria_ID' => 7,
    'pg' => 1,
    'fpg' => 3
);

$smarty->assign("urlBase", urlBase);
$smarty->assign("urlSite", urlSite);
$smarty->assign("nav", $nav);
$smarty->assign("dBanner", $Conteudo->Slide(2, 10));
$smarty->assign("dEntidades", $Conteudo->Entidades());
$smarty->assign("dGaleria", $Conteudo->Lista($dd));
$smarty->display('home.html');
