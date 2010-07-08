<?php
function smarty_function_uri($params)
{
	$alias = !empty($params['alias']) && validate::isnUmber($params['alias']) ? $params['alias'] : 0;
	return uri(!empty($params['args']) ? $params['args'] : '', $alias);
}
/* vim: set expandtab: */
?>