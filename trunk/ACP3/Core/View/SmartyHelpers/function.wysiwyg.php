<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */
function smarty_function_wysiwyg($params) {
	$params['id'] = !empty($params['id']) ? $params['id'] : $params['name'];

	$wysiwyg = new \ACP3\Core\WYSIWYG();
	\ACP3\Core\WYSIWYG::factory('TinyMCE', $params);
	return $wysiwyg->display();
}
/* vim: set expandtab: */