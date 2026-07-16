<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     modifier.formataMoeda.php
 * Type:     modifier
 * Name:     formataMoeda 
 * Purpose:  formata decimal para moeda local
 * -------------------------------------------------------------
 */
function smarty_modifier_formataMoeda($valor)
{
	return number_format($valor, 2, ',', '.');
}
?>
