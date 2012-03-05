<?php
function smarty_function_uri($params)
{
	global $uri;

	$alias = !empty($params['alias']) && ACP3_Validate::isNumber($params['alias']) === true ? $params['alias'] : 1;
	return $uri->route(!empty($params['args']) ? $params['args'] : '', (int) $alias);
}
/* vim: set expandtab: */
?>