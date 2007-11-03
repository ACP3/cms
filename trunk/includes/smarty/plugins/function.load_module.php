<?php
function smarty_function_load_module($params, &$smarty)
{
	global $auth, $breadcrumb, $cache, $config, $db, $modules, $tpl, $validate;

	$module = explode('|', $params['module']);
	$path = 'modules/' . $module[0] . '/' . $module[1] . '.php';

	if (is_file($path)) {
		include $path;
	}
}
/* vim: set expandtab: */
?>