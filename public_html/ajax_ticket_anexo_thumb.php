<?php
$diretorio = "";
include($diretorio."_config.inc.php");

$smarty = new Smarty;
$smarty -> muteExpectedErrors();

$smarty->template_dir = $diretorio.'templates';
$smarty->compile_dir = $diretorio.'templates_c';
$smarty->cache_dir = $diretorio.'cache';

if($_POST){
    $smarty->assign('urlBase', urlBase);
    $smarty->assign('id', $_POST['id']);
    $smarty->assign('caminho', $_POST['caminho']);
    $smarty->assign('nome', $_POST['nome']);
    $template = $smarty->fetch('ajax_ticket_anexo_thumb.html');

    $retorno = array('template' => $template);
    echo json_encode($retorno);
}