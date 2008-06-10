<?php
function smarty_function_lang($params)
{
	global $lang;
	$values = explode('|', $params['values']);
	return $lang->t($values[0], $values[1]);
}
/* vim: set expandtab: */
?>