<?php
function smarty_function_mark($params, $template)
{
	$template->assign('checkbox_name', $params['name']);
	return $template->fetch('common/mark.html');
}
/* vim: set expandtab: */
?>