<?php
$diretorio = "../../";
include($diretorio."_config.inc.php");

$smarty = new Smarty;
$smarty -> muteExpectedErrors();

$smarty->template_dir = $diretorio.'templates';
$smarty->compile_dir = $diretorio.'templates_c';
$smarty->cache_dir = $diretorio.'cache';

if($_POST){
    $smarty->assign('diretorio', $diretorio);
    $smarty->assign('id', $_POST['id']);
    $smarty->assign('midia', $_POST['midia']);
    $smarty->assign('destaque', $_POST['destaque']);
    $smarty->assign('tipo', $_POST['tipo']);
    $template = $smarty->fetch('website/conteudo/ajax_midia_thumb.html');

    $retorno = array('template' => $template);
    echo json_encode($retorno);
}