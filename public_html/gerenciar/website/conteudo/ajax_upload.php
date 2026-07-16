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

if($_REQUEST['acao'] == 'e' and $_SESSION['incremento_uploadify'] == 0) {
    $_SESSION['incremento_uploadify'] = 1;
}

$arquivo = $Conteudo->Upload($_FILES, $_REQUEST);
echo json_encode($arquivo);