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

sleep(1);

if($_POST){
    $Conteudo->DeletaMidia($_POST['id']);
    echo json_encode("ok");
}