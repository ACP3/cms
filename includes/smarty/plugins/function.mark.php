<?php
function smarty_function_mark($params, $template)
{
	$template->smarty->assign('checkbox_name', $params['name']);
	return $template->smarty->fetch('common/mark.html');
}
/* vim: set expandtab: */
?>