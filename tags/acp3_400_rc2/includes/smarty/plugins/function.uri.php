<?php
function smarty_function_uri($params)
{
	return uri(!empty($params['args']) ? $params['args'] : '');
}
/* vim: set expandtab: */
?>