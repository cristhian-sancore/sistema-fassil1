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
$Conteudo = new Conteudo($Conn);

if(!isset($_SESSION['_user'])){
    header("Location:".urlBase."index.php");
}

if($_POST){
    $dd = $Conteudo->EditaMidia($_POST);
    echo json_encode($dd);
}
