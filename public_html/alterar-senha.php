<?php
include("_config.inc.php");
include("gerenciar/libs/OrdemServicos.class.php");

$smarty = new Smarty;
$smarty -> muteExpectedErrors();

$Conn = new Database();
$Sessao = new Sessao();
$Generico = new Generico($Conn);
$Cadastro = new Cadastro($Conn);
$Email = new Email($Conn);

$dd = $Cadastro->VerificaCodigoSenha($nav['n2']);
if(!$dd){
    header("Location:".urlBase."404");
}

include("_login.inc.php");

if($_POST){
    $_POST['Cliente_ID'] = $dd['Cliente_ID'];
    $Cadastro->AlterarSenha($_POST);

    /*ENVIA EMAIL*/
    if($dd && $_SERVER['SERVER_NAME']!='localhost'){
        $dd['url'] = urlBase;
        $dd['senha'] = $_POST['senha'];

        $smarty->assign('dados', $dd);
        $template = $smarty->fetch('_email_cadastro.html');

        $dd['email'] = $dd['Cliente_Email'];
        $dd['titulo'] = "Grupo Fassil";
        $dd['assunto'] = "Alteração de senha";
        $Email->Enviar($dd, $template);
    }

    header("Location:".urlBase."recuperar-senha_ok/{$nav['n2']}");
}

$smarty->assign("urlBase", urlBase);
$smarty->assign("urlSite", urlSite);
$smarty->assign("nav", $nav);
$smarty->assign("dados", $dd);
$smarty->display('alterar-senha.html');
