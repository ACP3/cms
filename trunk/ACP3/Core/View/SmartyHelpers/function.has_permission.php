<?php
function smarty_function_has_permission($params)
{
	if (isset($params['mod'], $params['file'])) {
		return \ACP3\Core\Modules::hasPermission($params['mod'], $params['file']);
	} else {
		return false;
	}
}
/* vim: set expandtab: */