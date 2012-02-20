<?php
function smarty_function_lang($params)
{
	global $lang;
	$values = explode('|', $params['t']);
	return $lang->t($values[0], $values[1]);
}
/* vim: set expandtab: */