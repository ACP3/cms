<?php
function smarty_function_uri($params)
{
	global $uri;

	$alias = !empty($params['alias']) && validate::isNumber($params['alias']) ? $params['alias'] : 0;
	return $uri->route(!empty($params['args']) ? $params['args'] : '', $alias);
}
/* vim: set expandtab: */
?>