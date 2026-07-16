<?php
include("_config.inc.php");

$smarty = new Smarty;
$smarty -> muteExpectedErrors();

if($_POST) {
	$permissao = false;

	$response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6LfQATAUAAAAAHfaOiHdDLA3LKj-S_R2OZwt-lUU&response=".$_POST['captcha']."&remoteip=".$_SERVER['REMOTE_ADDR']);
	$responseData = json_decode($response);

	if($responseData->success) {
		$permissao = true;
	}

	echo json_encode(array('permissao' => $permissao));
}