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

$date = ' AND (start = end AND start <= \'' . date_aligned(2, time()) . '\' OR start != end AND start <= \'' . date_aligned(2, time()) . '\' AND end >= \'' . date_aligned(2, time()) . '\')';

if (!empty($modules->id) && $db->select('id', 'news', 'id = \'' . $modules->id . '\'' . $date, 0, 0, 0, 1) == 1) {
	if (isset($_POST['submit'])) {
		include_once 'modules/comments/entry.php';
	}
	if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
		// Input Felder von den Kommentaren
		$tpl->assign('form', isset($form) ? $form : '');

		// News Cache erstellen
		if (!$cache->check('news_details_id_' . $modules->id)) {
			$cache->create('news_details_id_' . $modules->id, $db->select('id, start, headline, text, cat, uri, target, link_title', 'news', 'id = \'' . $modules->id . '\''));
		}
		$news = $cache->output('news_details_id_' . $modules->id);

		// Brotkrümelspur
		$breadcrumb->assign(lang('news', 'news'), uri('news'));
		$category = $db->select('name', 'categories', 'id = \'' . $news[0]['cat'] . '\'');
		$breadcrumb->assign($category[0]['name'], uri('news/list/cat_' . $news[0]['cat']));
		$breadcrumb->assign($news[0]['headline']);

		$news[0]['date'] = date_aligned(1, $news[0]['start']);
		$news[0]['headline'] = $news[0]['headline'];
		$news[0]['text'] = $db->escape($news[0]['text'], 3);
		$news[0]['uri'] = $db->escape($news[0]['uri'], 3);
		$news[0]['target'] = $news[0]['target'] == '2' ? ' onclick="window.open(this.href); return false"' : '';

		$tpl->assign('news', $news[0]);

		if ($modules->check('comments', 'functions')) {
			include_once 'modules/comments/functions.php';

			$tpl->assign('comments_list', comments_list());
			$tpl->assign('comments_form', comments_form());
		}
		$content = $tpl->fetch('news/details.html');
	}
} else {
	redirect('errors/404');
}
?>