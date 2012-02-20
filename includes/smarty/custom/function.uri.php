<?php
function smarty_function_uri($params)
{
	global $uri;

	$alias = !empty($params['alias']) && validate::isNumber($params['alias']) === true ? $params['alias'] : 0;
	return $uri->route(!empty($params['args']) ? $params['args'] : '', (int) $alias);
}
/* vim: set expandtab: */
?>