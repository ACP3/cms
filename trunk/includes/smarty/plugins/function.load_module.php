<?php
function smarty_function_load_module($params, &$smarty)
{
	global $auth, $db, $modules, $tpl;

	$module = explode('|', $params['module']);

	if ($modules->check($module[0], $module[1])) {
		include ACP3_ROOT . 'modules/' . $module[0] . '/' . $module[1] . '.php';
	}
}
/* vim: set expandtab: */
?>