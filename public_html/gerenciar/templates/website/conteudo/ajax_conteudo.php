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
$Menu = new Menu($Conn);
$Conteudo = new Conteudo($Conn);

$Mod[2] = "conteudo";

include($diretorio."_config_permissao.inc.php");

$Conteudo_ID = 0;

if($_POST){
    $ac = strip_tags(trim($_POST['acao']));

    if($ac=='i' && in_array($Mod_ID."a", $_SESSION['_user']['Permissao_Modulos'])){
        $Conteudo_ID = $Conteudo->Grava($_POST);
    }elseif($ac=='e' && in_array($Mod_ID."e", $_SESSION['_user']['Permissao_Modulos'])){
        $Conteudo_ID = $Conteudo->Edita($_POST);
    }
}

echo json_encode(array('Conteudo_ID' => $Conteudo_ID));