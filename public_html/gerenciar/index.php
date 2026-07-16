<?php
include("_config.inc.php");

$smarty = new Smarty;
$smarty -> muteExpectedErrors();

$Sessao = new Sessao();
$Conn = new Database();

if($_POST){
	$login = addslashes(trim($_POST['login']));
	$senha = addslashes($_POST['senha']);

	$params = array($login, $senha);
	$dados = $Conn->execReader("SELECT 
									t1.Usuario_ID, 
									t1.Empresa_ID, 
									t1.Permissao_ID, 
									t1.Usuario_Nome, 
									t1.Usuario_Login, 
									t1.Usuario_Tipo, 
									date_format(t1.Usuario_LastAcesso, '%d/%m/%Y %H:%i') as Usuario_LastAcesso,
									t2.Permissao_Modulos
								FROM 
									usuarios as t1
									LEFT JOIN permissoes as t2 on (t2.Permissao_ID=t1.Permissao_ID)
									LEFT JOIN empresas as t3 on (t3.Empresa_ID=t1.Empresa_ID)
								WHERE 
									t1.Usuario_Login=? AND 
									t1.Usuario_Senha=sha1(?) AND 
									t1.Usuario_Status=1 AND 
									t1.Permissao_ID!=0 AND 
									t3.Empresa_Status=1", $params, 3);
	if($dados){
		//Atualiza último Acesso
		$Conn->execWrite("UPDATE usuarios SET Usuario_LastAcesso=now()", null);

		/* informacoes do usuario */
		$_SESSION['_user']['Empresa_ID'] = $dados['Empresa_ID'];
		$_SESSION['_user']['Permissao_ID'] = $dados['Permissao_ID'];
		$_SESSION['_user']['Permissao_Modulos'] = explode('*',$dados['Permissao_Modulos']);
		$_SESSION['_user']['Usuario_ID'] = $dados['Usuario_ID'];
		$_SESSION['_user']['Usuario_Nome'] = $dados['Usuario_Nome'];
		$_SESSION['_user']['Usuario_Login'] = $dados['Usuario_Login'];
		$_SESSION['_user']['Usuario_Tipo'] = $dados['Usuario_Tipo'];
		$_SESSION['_user']['Usuario_LastAcesso'] = $dados['Usuario_LastAcesso'];

		/* Seta um cookie para encerrar quando usuario fechar a pagina*/
		//setcookie("_user", $login, 0);

		/*echo"<pre>";
		print_r($_SESSION);
		DIE;*/

		header("Location:dashboard.php");
		die();
	}else{
		$Erro = array("class" => "trigger-error",
					  "titulo" => "Ops",
					  "msg" => "Para efetuar Login no sistema é preciso informar o usuário e senha de acesso. Obrigado!");

		$smarty->assign('urlBase', urlBase);
		$smarty->assign('Erro', $Erro);
		$smarty->display('login/login.html');
		die();
	}
}

if(isset($_SESSION['_user'])){
	header("Location:dashboard.php");
}else{
	$smarty->assign('urlSite', urlSite);
	$smarty->assign('urlBase', urlBase);
	$smarty->assign('Info', $Info);
	$smarty->display('login/login.html');
}