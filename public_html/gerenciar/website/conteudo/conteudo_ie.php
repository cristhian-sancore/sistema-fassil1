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

include($diretorio."_config_permissao.inc.php");

if($_FILES) {
    if($_REQUEST['acao'] == 'e' and $_SESSION['incremento_uploadify'] == 0) {
        $_SESSION['incremento_uploadify'] = 1;
    }

    $arquivo = $Conteudo->Upload($_FILES, $_REQUEST);
    echo json_encode($arquivo);
}else{
    $ac = (isset($_REQUEST['ac']))?strip_tags(trim($_REQUEST['ac'])):null;
    $id = 0;
    if(isset($_REQUEST['id'])){
        $id = strip_tags(trim($_REQUEST['id']));
    }

    $_SESSION['incremento_uploadify'] = 0;

    if(!empty($_REQUEST['Conteudo_ID'])) {
        //if($galeria->validar($_REQUEST['Conteudo_ID'])) {
            $dados = $Conteudo->Dados($_REQUEST['Conteudo_ID']);
        /*}else{
        	header("Location:".urlBase."$Mod[0]/$Mod[1]/conteudo.php");
            exit;
        }*/
    }

    if(!empty($dados['metadado'])) {
        $metadado = $dados['metadado'];
    }else{
        $metadado = substr(md5(uniqid(rand(0, 1000000))), 0, 6);
    }

    $smarty->assign('diretorio', $diretorio);
    $smarty->assign('urlBase', urlBase);
    $smarty->assign('urlMidia', urlMidia);
    $smarty->assign('Mod', $Mod);
    $smarty->assign('MenuBar', $Menu->MenuBar());
    $smarty->assign('Info', $Info);

    $smarty->assign('categoria_ie', $Conteudo->ListaIE());
    $smarty->assign('ac', $ac);
    $smarty->assign('session_id', session_id());
    $smarty->assign('metadado', $metadado);
    $smarty->assign('dados', $Conteudo->Dados($id));
    $smarty->assign('data', date("d/m/Y"));
    $smarty->display('website/conteudo/conteudo_ie.html');
}