<?php
/**
 * Files
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit;

if (modules::check('categories', 'functions') == 1) {
	require_once MODULES_DIR . 'categories/functions.php';
	$categories = getCategoriesCache('files');
	if (count($categories) > 0) {
		$tpl->assign('categories', $categories);
	}
}

$content = modules::fetchTemplate('files/list.html');
