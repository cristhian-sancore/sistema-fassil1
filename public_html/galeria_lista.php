<?php
include("_config.inc.php");

$smarty = new Smarty;
$smarty -> muteExpectedErrors();

$Conn = new Database();
$Sessao = new Sessao();
$Generico = new Generico($Conn);
$Conteudo = new Conteudo($Conn);

include("_login.inc.php");

$get = explode('_', $nav['n1']);
$busca = (isset($get[2]))?$get[2]:'';

$dd = array(
    'Categoria_ID' => 7,
    'pg' => (isset($get[1]))?$get[1]:1,
    'fpg' => 9,
    'link' => urlBase."galeria-de-fotos"
);

$smarty->assign("urlBase", urlBase);
$smarty->assign("urlSite", urlSite);
$smarty->assign("nav", $nav);
$smarty->assign('Info', $Info);

$smarty->assign("lista", $Conteudo->Lista($dd));
$smarty->display('galeria_lista.html');
