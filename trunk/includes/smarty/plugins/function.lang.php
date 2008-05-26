<?php
function smarty_function_lang($params)
{
	$values = explode('|', $params['values']);
	return lang($values[0], $values[1]);
}
/* vim: set expandtab: */
?>