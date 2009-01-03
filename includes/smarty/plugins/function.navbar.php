<?php
function smarty_function_navbar($params)
{
	if (modules::check('menu_items', 'functions')) {
		include_once ACP3_ROOT . 'modules/menu_items/functions.php';
		return processNavbar($params['block']);
	}
	return '';
}
/* vim: set expandtab: */
?>