<?php
include("_config.inc.php");

$smarty = new Smarty;
$smarty -> muteExpectedErrors();

$Conn = new Database();
$Sessao = new Sessao();
$Cidades = new Cidades($Conn);

if($_POST) {
	$subcat = $Cidades->CidadesEstados($_POST['cod']);
}

?>

	<option value="0">Selecione</option>
<? for($i = 0; $i < count($subcat); $i++): ?>
	<option value="<? echo $subcat[$i]['Cidade_ID'] ?>"><? echo $subcat[$i]['Cidade_Nome'] ?></option>
<? endfor ?>