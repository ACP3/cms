<?php
/**
 * News
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

if (!empty($modules->id) && $db->select('id', 'news', 'id = \'' . $modules->id . '\'', 0, 0, 0, 1) == '1') {
	if (isset($_POST['submit'])) {
		include 'modules/news/entry.php';
	}
	if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
		$news = $db->select('start, end, headline, text, category_id, uri, target, link_title', 'news', 'id = \'' . $modules->id . '\'');
		$news[0]['text'] = $db->escape($news[0]['text'], 3);

		// Datumsauswahl
		$tpl->assign('start_date', publication_period('start', $news[0]['start']));
		$tpl->assign('end_date', publication_period('end', $news[0]['end']));

		// Kategorien
		if (!$cache->check('categories_news')) {
			$cache->create('categories_news', $db->select('id, name, description', 'categories', 'module = \'news\'', 'name ASC'));
		}
		$categories = $cache->output('categories_news');
		$c_categories = count($categories);

		if ($c_categories > 0) {
			for ($i = 0; $i < $c_categories; $i++) {
				$categories[$i]['selected'] = select_entry('cat', $categories[$i]['id'], $news[0]['category_id']);
				$categories[$i]['name'] = $categories[$i]['name'];
			}
			$tpl->assign('categories', $categories);
		}

		// Linkziel
		$target[0]['value'] = '1';
		$target[0]['selected'] = select_entry('target', '1', $news[0]['target']);
		$target[0]['lang'] = lang('news', 'window_self');
		$target[1]['value'] = '2';
		$target[1]['selected'] = select_entry('target', '2', $news[0]['target']);
		$target[1]['lang'] = lang('news', 'window_blank');
		$tpl->assign('target', $target);

		$tpl->assign('form', isset($form) ? $form : $news[0]);

		$content = $tpl->fetch('news/edit.html');
	}
} else {
	redirect('errors/404');
}
?>