<?php
function smarty_function_navbar($params)
{
	global $modules;
	if ($modules->check('pages', 'functions')) {
		include_once ACP3_ROOT . 'modules/pages/functions.php';
		if (!cache::check('pages')) {
			cache::create('pages', generatePagesCache());
		}
		return processNavbar(cache::output('pages'), $params['block']);
	}
	return '';
}
/* vim: set expandtab: */
?>