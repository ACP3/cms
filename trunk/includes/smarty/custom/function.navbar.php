<?php
function smarty_function_navbar($params)
{
	if (ACP3_Modules::check('menu_items', 'functions') === true) {
		include_once MODULES_DIR . 'menu_items/functions.php';
		return processNavbar($params['block']);
	}
	return '';
}
/* vim: set expandtab: */
?>