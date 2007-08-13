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

if (!$cache->check('categories_dl')) {
	$cache->create('categories_dl', $db->select('id, name, description', 'categories', 'module = \'dl\''));
}
$categories = $cache->output('categories_dl');

if (count($categories) > 0) {
	$tpl->assign('categories', $categories);
}
$content = $tpl->fetch('dl/list.html');
?>