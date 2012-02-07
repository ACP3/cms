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

if (modules::check('categories', 'functions') === true) {
	require_once MODULES_DIR . 'categories/functions.php';
	$categories = getCategoriesCache('files');
	if (count($categories) > 0) {
		$tpl->assign('categories', $categories);
	}
}

view::setContent(view::fetchTemplate('files/list.tpl'));
