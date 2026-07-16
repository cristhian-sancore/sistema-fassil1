<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     modifier.utf8Decode.php
 * Type:     modifier
 * Name:     utf8Descode
 * Purpose:  altera para utf8Decode
 * -------------------------------------------------------------
 */
function smarty_modifier_utf8Decode($valor)
{
	return utf8_decode($valor);
}
?>