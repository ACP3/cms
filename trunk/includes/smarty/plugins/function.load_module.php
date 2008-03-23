<?php
function smarty_function_load_module($params, &$smarty)
{
	global $auth, $breadcrumb, $cache, $config, $db, $modules, $tpl, $validate;

	$module = explode('|', $params['module']);
	$path = ACP3_ROOT . 'modules/' . $module[0] . '/' . $module[1] . '.php';

	if (file_exists($path)) {
		include $path;
	}
}
/* vim: set expandtab: */
?>