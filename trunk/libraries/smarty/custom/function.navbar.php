<?php
function smarty_function_navbar($params)
{
	if (ACP3_Modules::check('menus', 'functions') === true) {
		include_once MODULES_DIR . 'menus/functions.php';
		return processNavbar($params['block'], isset($params['use_bootstrap']) ? (bool)$params['use_bootstrap'] : true, !empty($params['class']) ? $params['class'] : '');
	}
	return '';
}
/* vim: set expandtab: */
?>