<?php
include("_config.inc.php");
include("gerenciar/libs/OrdemServicos.class.php");

$smarty = new Smarty;
$smarty -> muteExpectedErrors();

$Conn = new Database();
$Sessao = new Sessao();
$Generico = new Generico($Conn);
$Conteudo = new Conteudo($Conn);
$OrdemServicos = new OrdemServicos($Conn);
$Email = new Email($Conn);

include("_verifica_login.inc.php");

$dd = array(
    'Protocolo' => $nav['n3'],
    'Cliente_ID' => $_SESSION['xxx']['login']['Cliente_ID']
);

$Dados = $Generico->DadosOS($dd);
if(!$Dados){
    header("Location:".urlBase."painel");
}

if($_POST){
    $_POST['Cliente_ID'] = $_SESSION['xxx']['login']['Cliente_ID'];
    $OrdemServicos->Confirma_Fechamento_OS($_POST);
    $dd = $OrdemServicos->Dados($_POST['os'], true);

    /*ENVIA EMAIL*/
    if($_POST['os'] && $_SERVER['SERVER_NAME']!='localhost' && $dd['Status_ID']==3){
        $dd['url'] = urlBase;

        $smarty->assign('dados', $dd);
        $template = $smarty->fetch('_email_ticket_cadastro.html');

        $Email->Ticket($dd, $template);
    }

    header('Refresh:0');
}else{
    //TOKEN
    include("_token.inc.php");
}

$total['not'] = $Generico->Estatisticas("os_fechamentos", "Cliente_ID={$_SESSION['xxx']['login']['Cliente_ID']} AND Fechamento_Status=0");

$smarty->assign("urlBase", urlBase);
$smarty->assign("nav", $nav);
$smarty->assign('Info', $Info);

$smarty->assign("total", $total);
$smarty->assign('dados', $Dados);
$smarty->assign('msg', $OrdemServicos->Mensagens($Dados['OS_ID'], 0));
$smarty->display('ticket_view.html');
