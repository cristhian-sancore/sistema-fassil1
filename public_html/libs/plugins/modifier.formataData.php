<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     modifier.formataData.php
 * Type:     modifier
 * Name:     formataData
 * Purpose:  formata data para padrão brasileiro
 * -------------------------------------------------------------
 */
function smarty_modifier_formataData($data)
{
	$foo = explode('-', $data);
	$bar = $foo[2]."/".$foo[1]."/".$foo[0];
	return $bar;
}
?>
