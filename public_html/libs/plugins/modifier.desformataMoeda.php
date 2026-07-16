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
function smarty_modifier_desformataMoeda($valor)
{
	return str_replace(',','.', str_replace('.','', $valor));
}
?>