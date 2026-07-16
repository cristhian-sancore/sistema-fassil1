<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     modifier.utf8Encode.php
 * Type:     modifier
 * Name:     utf8Descode
 * Purpose:  altera para utf8Encode
 * -------------------------------------------------------------
 */
function smarty_modifier_utf8Encode($valor)
{
	return utf8_encode($valor);
}
?>