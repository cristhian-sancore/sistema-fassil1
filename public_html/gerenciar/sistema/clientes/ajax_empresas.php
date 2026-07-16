<?php
$diretorio = "../../";
include($diretorio."_config.inc.php");

$smarty = new Smarty;
$smarty -> muteExpectedErrors();

$smarty->template_dir = $diretorio.'templates';
$smarty->compile_dir = $diretorio.'templates_c';
$smarty->cache_dir = $diretorio.'cache';

$Conn = new Database();
$Sessao = new Sessao();
$Cidades = new Cidades($Conn);

if($_POST) {
	$dd = $Cidades->CidadesEmpresas($_POST['cod']);
}

?>

	<option value="0">Selecione</option>
<? for($i = 0; $i < count($dd); $i++): ?>
	<option value="<? echo $dd[$i]['CE_ID'] ?>" <? if($_POST['subempresa'] == $dd[$i]['CE_ID']){?>selected<?}?>><? echo $dd[$i]['CE_Nome'] ?></option>
<? endfor ?>