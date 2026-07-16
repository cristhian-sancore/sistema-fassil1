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

include("_login.inc.php");

$dd = null;
if($_POST){
    $dd = $Cadastro->RecuperarSenha($_POST);

    /*ENVIA EMAIL*/
    if($dd && $_SERVER['SERVER_NAME']!='localhost'){
        $dd['url'] = urlBase;

        $smarty->assign('dados', $dd);
        $template = $smarty->fetch('_email_recuperar_senha.html');

        $dd['email'] = $dd['Cliente_Email'];
        $dd['titulo'] = "Grupo Fassil";
        $dd['assunto'] = "Recuperação de senha";
        $Email->Enviar($dd, $template);
    }

    header("Location:".urlBase."recuperar-senha_ok");
}

$smarty->assign("urlBase", urlBase);
$smarty->assign("urlSite", urlSite);
$smarty->assign("nav", $nav);
$smarty->assign("dados", $dd);
$smarty->display('recuperar-senha.html');
