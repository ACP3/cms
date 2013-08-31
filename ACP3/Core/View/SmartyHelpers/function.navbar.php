<?php
function smarty_function_navbar($params)
{
	if (\ACP3\Core\Modules::isActive('menus') === true) {
		return ACP3\Modules\Menus\MenusHelpers::processNavbar(
				$params['block'],
				isset($params['use_bootstrap']) ? (bool)$params['use_bootstrap'] : true,
				!empty($params['class']) ? $params['class'] : '',
				!empty($params['classDropdownLi']) ? $params['classDropdownLi'] : ''
			);
	}
	return '';
}
/* vim: set expandtab: */