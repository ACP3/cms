<?php
function smarty_function_icon($params, &$smarty)
{
	return ROOT_DIR . 'images/crystal/' . $params['path'] . '.png';
}
/* vim: set expandtab: */
?>