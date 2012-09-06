<?php
function smarty_function_load_module($params)
{
	$module = explode('|', $params['module']);

	if (ACP3_Modules::check($module[0], $module[1]) === true) {
		include MODULES_DIR . $module[0] . '/' . $module[1] . '.php';
	}
}
/* vim: set expandtab: */