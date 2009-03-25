<?php
/**
 * Files
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3'))
	exit;

if (modules::check('categories', 'functions') == 1) {
	$categories = getCategoriesCache('files');
	if (count($categories) > 0) {
		$tpl->assign('categories', $categories);
	}
}

$content = $tpl->fetch('files/list.html');
?>