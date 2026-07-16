<?php
$diretorio = "../../";
include($diretorio . "_config.inc.php");

$smarty = new Smarty;
$smarty -> muteExpectedErrors();

$smarty->template_dir = $diretorio.'templates';
$smarty->compile_dir = $diretorio.'templates_c';
$smarty->cache_dir = $diretorio.'cache';

$Sessao = new Sessao();
$Conn = new Database();
$Menu = new Menu($Conn);
$Clientes = new Clientes($Conn);

if(!isset($_SESSION['_user'])){
	header("Location:".urlBase."index.php");
}


$backupfile = 'Autobackup_' . date("Ymd") . '.sql';
$backupzip = $backupfile . '.tar.gz';
system("mysqldump -h ".HOST." -u ".USER." -p ".PASS." --lock-tables ".DBSA." > $backupfile");
system("tar -czvf $backupzip $backupfile");

header("Location:".urlBase."index.php");