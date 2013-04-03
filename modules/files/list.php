<?php
/**
 * Files
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit;

if (ACP3_Modules::check('categories', 'functions') === true) {
	require_once MODULES_DIR . 'categories/functions.php';
	$categories = getCategoriesCache('files');
	if (count($categories) > 0) {
		ACP3_CMS::$view->assign('categories', $categories);
	}
}