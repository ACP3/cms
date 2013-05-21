<?php
function smarty_function_load_module($params)
{
	$module = explode('|', $params['module']);

	if (\ACP3\Core\Modules::hasPermission($module[0], $module[1]) === true) {
		$className = "\\ACP3\\Modules\\" . ucfirst($module[0]) . "\\" . ucfirst($module[0]) . 'Frontend';
		$action = 'action' . ucfirst($module[1]);
		$mod = new $className();
		$mod->$action();
	}
}
/* vim: set expandtab: */