<?php
function smarty_function_uri($params, &$smarty)
{
	global $uri;
	return uri(!empty($params['args']) ? $params['args'] : '');
}
/* vim: set expandtab: */
?>