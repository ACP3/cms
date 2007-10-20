<?php
/**
 * News
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3'))
	exit;

if (isset($_POST['submit'])) {
	include 'modules/news/entry.php';
}
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
	// Datumsauswahl
	$tpl->assign('start_date', publication_period('start'));
	$tpl->assign('end_date', publication_period('end'));

	// Kategorien
	if (!$cache->check('categories_news')) {
		$cache->create('categories_news', $db->select('id, name, description', 'categories', 'module = \'news\'', 'name ASC'));
	}
	$categories = $cache->output('categories_news');
	$c_categories = count($categories);

	if ($c_categories > 0) {
		for ($i = 0; $i < $c_categories; $i++) {
			$categories[$i]['selected'] = select_entry('cat', $categories[$i]['id']);
			$categories[$i]['name'] = $categories[$i]['name'];
		}
		$tpl->assign('categories', $categories);
	}

	// Linkziel
	$target[0]['value'] = '1';
	$target[0]['selected'] = select_entry('target', '1');
	$target[0]['lang'] = lang('news', 'window_self');
	$target[1]['value'] = '2';
	$target[1]['selected'] = select_entry('target', '2');
	$target[1]['lang'] = lang('news', 'window_blank');
	$tpl->assign('target', $target);

	$tpl->assign('form', isset($form) ? $form : '');

	$content = $tpl->fetch('news/acp_create.html');
}
?>