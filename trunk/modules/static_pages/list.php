<?php
/**
 * Static Pages
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3'))
	exit;

if (validate::isNumber($uri->id) || $db->select('COUNT(id)', 'static_pages', 'id = \'' . $uri->id . '\'', 0, 0, 0, 1) == 1) {
	$page = $db->select('title, text', 'static_pages', 'id = \'' . $uri->id . '\'');

	breadcrumb::assign($db->escape($page[0]['title'], 3));

	$tpl->assign('text', $db->escape($page[0]['text'], 3));
	$content = $tpl->fetch('static_pages/list.html');
} else {
	redirect('errors/404');
}
?>