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

if (modules::check('categories', 'functions')) {
	include_once ACP3_ROOT . 'modules/categories/functions.php';
	$tpl->assign('categories', categoriesList('files', 'list'));
}

$content = $tpl->fetch('files/list.html');
?>