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

$date = ' AND (start = end AND start <= \'' . dateAligned(2, time()) . '\' OR start != end AND start <= \'' . dateAligned(2, time()) . '\' AND end >= \'' . dateAligned(2, time()) . '\')';

if (validate::isNumber($uri->id) && $db->select('id', 'news', 'id = \'' . $uri->id . '\'' . $date, 0, 0, 0, 1) == 1) {
	// News Cache erstellen
	if (!cache::check('news_details_id_' . $uri->id)) {
		cache::create('news_details_id_' . $uri->id, $db->select('id, start, headline, text, category_id, uri, target, link_title', 'news', 'id = \'' . $uri->id . '\''));
	}
	$news = cache::output('news_details_id_' . $uri->id);

	// Brotkrümelspur
	$category = $db->select('name', 'categories', 'id = \'' . $news[0]['category_id'] . '\'');
	breadcrumb::assign($lang->t('news', 'news'), uri('news'));
	if (count($category) > 0) {
		breadcrumb::assign($category[0]['name'], uri('news/list/cat_' . $news[0]['category_id']));
	}
	breadcrumb::assign($news[0]['headline']);

	$news[0]['date'] = dateAligned(1, $news[0]['start']);
	$news[0]['headline'] = $news[0]['headline'];
	$news[0]['text'] = $db->escape($news[0]['text'], 3);
	$news[0]['uri'] = $db->escape($news[0]['uri'], 3);
	$news[0]['target'] = $news[0]['target'] == '2' ? ' onclick="window.open(this.href); return false"' : '';

	$tpl->assign('news', $news[0]);

	if (modules::check('comments', 'functions')) {
		include_once ACP3_ROOT . 'modules/comments/functions.php';

		$tpl->assign('comments', comments('news', $uri->id));
	}
	$content = $tpl->fetch('news/details.html');
} else {
	redirect('errors/404');
}
?>