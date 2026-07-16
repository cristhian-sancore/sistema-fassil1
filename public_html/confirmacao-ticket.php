<?php
include("_config.inc.php");
include("gerenciar/libs/OrdemServicos.class.php");

$smarty = new Smarty;
$smarty -> muteExpectedErrors();

$Conn = new Database();
$Sessao = new Sessao();
$Generico = new Generico($Conn);
$OrdemServicos = new OrdemServicos($Conn);
$Email = new Email($Conn);

$dd = $Generico->DadosOS_Confirmacao($nav['n2'], $nav['n3']);
if(!$dd['OS_ID']){
    header("Location:".urlBase."404");
}

if($_POST){
    $dados['os'] = $dd['OS_ID'];
    $dados['Cliente_ID'] = $dd['Cliente_ID_Fechamento'];
    $OrdemServicos->Confirma_Fechamento_OS($dados);

    /*ENVIA EMAIL*/
    if($dados['os'] && $_SERVER['SERVER_NAME']!='localhost' && $dd['Status_ID']>=3){
        $dd = $Generico->DadosOS_Confirmacao($nav['n2'], $nav['n3']);
        $dd['url'] = urlBase;

        $smarty->assign('dados', $dd);
        $template = $smarty->fetch('_email_ticket_cadastro.html');

        $Email->Ticket($dd, $template);
    }

    header("Location:".urlBase."confirmacao-ticket/{$nav['n2']}/{$nav['n3']}");
}

$do = $OrdemServicos->Dados($dd['OS_ID'], true);
$do['Fechamento_Status'] = $dd['Fechamento_Status'];

$smarty->assign("urlBase", urlBase);
$smarty->assign("urlSite", urlSite);
$smarty->assign("nav", $nav);
$smarty->assign("dados", $do);
$smarty->display('confirmacao-ticket.html');
