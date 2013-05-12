<?php
function smarty_function_lang($params)
{
	$values = explode('|', $params['t']);
	return \ACP3\CMS::$injector['Lang']->t($values[0], $values[1]);
}
/* vim: set expandtab: */