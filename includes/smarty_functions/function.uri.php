<?php
function smarty_function_uri($params)
{
	$alias = isset($params['alias']) && ACP3_Validate::isNumber($params['alias']) === true ? $params['alias'] : 1;
	return ACP3_CMS::$uri->route(!empty($params['args']) ? $params['args'] : '', (int) $alias);
}
/* vim: set expandtab: */