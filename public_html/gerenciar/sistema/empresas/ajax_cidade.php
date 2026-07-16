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
	$subcat = $Cidades->CidadesEstados($_POST['cod']);
}

?>

	<option value="0">Selecione</option>
<? for($i = 0; $i < count($subcat); $i++): ?>
	<option value="<? echo $subcat[$i]['Cidade_ID'] ?>" <? if($_POST['subcat']  == $subcat[$i]['Cidade_ID']){?>selected<?}?>><? echo $subcat[$i]['Cidade_Nome'] ?></option>
<? endfor ?>