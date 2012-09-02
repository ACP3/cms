<?php
function smarty_function_lang($params)
{
	$values = explode('|', $params['t']);
	return ACP3_CMS::$lang->t($values[0], $values[1]);
}
/* vim: set expandtab: */