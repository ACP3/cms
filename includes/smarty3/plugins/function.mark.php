<?php
function smarty_function_mark($params, &$smarty)
{
	$smarty->assign('checkbox_name', $params['name']);
	return $smarty->fetch('common/mark.html');
}
/* vim: set expandtab: */
?>