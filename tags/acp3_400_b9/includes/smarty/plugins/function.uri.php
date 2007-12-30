<?php
function smarty_function_uri($params, &$smarty)
{
	return uri(!empty($params['args']) ? $params['args'] : '');
}
/* vim: set expandtab: */
?>