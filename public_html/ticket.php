<?php
include("_config.inc.php");
include("gerenciar/libs/OrdemServicos.class.php");

$smarty = new Smarty;
$smarty -> muteExpectedErrors();

$Conn = new Database();
$Sessao = new Sessao();
$Generico = new Generico($Conn);
$Email = new Email($Conn);
$OrdemServicos = new OrdemServicos($Conn);

include("_verifica_login.inc.php");

if($_POST){
    $_POST['empresa_id'] = $_SESSION['xxx']['login']['Empresa_ID'];
    $_POST['cliente_id'] = $_SESSION['xxx']['login']['Cliente_ID'];
    $_POST['data_inicio'] = date('d/m/Y');
    $_POST['horas_inicio'] = date('H:i');
    
    // gerando numero de protocolo
    $_POST['protocolo'] = $OrdemServicos->Protocolo() . $protocolo = date('AHis');
    
    $_POST['descricao'] = $_POST['descricao'];

    $Ticket_ID = $OrdemServicos->Grava($_POST);
    $dd = $OrdemServicos->Dados($Ticket_ID, true);

    /*ENVIA EMAIL*/
    if($Ticket_ID && $_SERVER['SERVER_NAME']!='localhost'){
        $dd['url'] = urlBase;

        $smarty->assign('dados', $dd);
        $template = $smarty->fetch('_email_ticket_cadastro.html');

        $Email->Ticket($dd, $template);
    }

    header("Location:".urlBase."painel/ticket/{$dd['OS_Protocolo']}");
}else{
    //TOKEN
    include("_token.inc.php");
}

$total['not'] = $Generico->Estatisticas("os_fechamentos", "Cliente_ID={$_SESSION['xxx']['login']['Cliente_ID']} AND Fechamento_Status=0");

$smarty->assign("urlBase", urlBase);
$smarty->assign("urlSite", urlSite);
$smarty->assign("nav", $nav);
$smarty->assign('Info', $Info);

$smarty->assign("total", $total);
$smarty->assign('prioridades', $OrdemServicos->Prioridades($_SESSION['xxx']['login']['Empresa_ID']));
$smarty->assign('grupos', $OrdemServicos->Grupos($_SESSION['xxx']['login']['Empresa_ID']));
$smarty->assign('tipos', $OrdemServicos->Tipos($_SESSION['xxx']['login']['Empresa_ID']));
$smarty->display('ticket.html');
