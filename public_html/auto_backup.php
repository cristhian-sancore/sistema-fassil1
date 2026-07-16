<?php
$diretorio = "";
include($diretorio . "_config.inc.php");

$smarty = new Smarty;
$smarty -> muteExpectedErrors();

$smarty->template_dir = $diretorio.'templates';
$smarty->compile_dir = $diretorio.'templates_c';
$smarty->cache_dir = $diretorio.'cache';

$Conn = new Database();
$Email = new Email($Conn);

$Arquivo = 'Backup_'.date("d-m-Y").'.sql';
$Zip = $Arquivo.'.tar.gz';

/*VERIFICA SE EXISTE ARQUIVO*/
if (file_exists($Arquivo)){
    unlink($Arquivo);
}

echo"Iniciando processo de backup...<br>";

/*FAZ BACKUP NO SERVIDOR*/
system("mysqldump -h ".HOST." -u ".USER." -p".PASS." --lock-tables ".DBSA." > $Arquivo");
system("tar -czvf $Zip $Arquivo");

/*MOVE PARA PASTA*/
copy($Zip, "backup/".$Zip);

/*Remover o arquivo do servidor (opcional)*/
unlink($Zip);
unlink($Arquivo);

/*ENVIA E-MAIL*/
if($_SERVER['SERVER_NAME']!='localhost'){
    $dados['url'] = urlBase;
    $dados['data'] = date("d/m/Y H:i");
    $dados['arquivo'] = $Zip;

    $smarty->assign('dados', $dados);
    $template = $smarty->fetch('_email_backup.html');

    $Email->Backup($dados, $template);
}

echo"<br>Backup finalizado!";

?>