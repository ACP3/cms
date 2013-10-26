<?php
function smarty_function_navbar($params)
{
	if (\ACP3\Core\Modules::isActive('menus') === true) {
		return ACP3\Modules\Menus\Helpers::processNavbar(
				$params['block'],
				isset($params['use_bootstrap']) ? (bool)$params['use_bootstrap'] : true,
				!empty($params['class']) ? $params['class'] : '',
				!empty($params['dropdownItemClass']) ? $params['dropdownItemClass'] : '',
				!empty($params['tag']) ? $params['tag'] : 'ul',
				isset($params['itemTag']) ? $params['itemTag'] : 'li',
				!empty($params['dropdownWrapperTag']) ? $params['dropdownWrapperTag'] : 'li',
				!empty($params['classLink']) ? $params['classLink'] : '',
				!empty($params['inlineStyles']) ? $params['inlineStyles'] : ''
			);
	}
	return '';
}
/* vim: set expandtab: */