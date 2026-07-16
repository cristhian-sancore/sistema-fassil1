<?php
include("_config.inc.php");

$smarty = new Smarty;
$smarty -> muteExpectedErrors();

$Conn = new Database();
$Sessao = new Sessao();
$Cidades = new Cidades($Conn);

if($_POST) {
	$dd = $Cidades->CidadesEmpresas($_POST['cod']);
}

?>

	<option value="0">Selecione</option>
<? for($i = 0; $i < count($dd); $i++): ?>
	<option value="<? echo $dd[$i]['CE_ID'] ?>"><? echo $dd[$i]['CE_Nome'] ?></option>
<? endfor ?>