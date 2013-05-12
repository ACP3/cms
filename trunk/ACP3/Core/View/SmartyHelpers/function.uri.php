<?php
function smarty_function_uri($params)
{
	$alias = isset($params['alias']) && \ACP3\Core\Validate::isNumber($params['alias']) === true ? $params['alias'] : 1;
	return \ACP3\CMS::$injector['URI']->route(!empty($params['args']) ? $params['args'] : '', (int) $alias);
}
/* vim: set expandtab: */