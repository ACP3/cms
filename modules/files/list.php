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
	$tpl->assign('categories', getCategoriesCache('files'));
}

$content = $tpl->fetch('files/list.html');
?>