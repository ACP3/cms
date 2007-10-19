<?php
/**
 * News
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_FRONTEND'))
	exit;

// Newsletter
$tpl->assign('MOD_newsletter', $modules->check('newsletter', 'create'));

//
$tpl->assign('MOD_feeds', $modules->check('feeds', 'list'));

$cat = isset($_POST['form']['cat']) && $validate->is_number($_POST['form']['cat']) ? $_POST['form']['cat'] : $modules->cat;

// Cache für die Kategorien
if (!$cache->check('categories_news')) {
	$cache->create('categories_news', $db->select('id, name, description', 'categories', 'module = \'news\'', 'name ASC'));
}
$categories = $cache->output('categories_news');
$c_categories = count($categories);

if ($c_categories > 0) {
	for ($i = 0; $i < $c_categories; $i++) {
		$categories[$i]['selected'] = select_entry('cat', $categories[$i]['id'], $cat);
		$categories[$i]['name'] = $categories[$i]['name'];
		if ($categories[$i]['id'] == $cat) {
				$breadcrumb->assign(lang('news', 'news'), uri('news'));
				$breadcrumb->assign($categories[$i]['name']);
		}
	}
	$tpl->assign('categories', $categories);
}

// Falls Kategorie angegeben, News nur aus eben jener selektieren
$cat_field = !empty($cat) ? ' AND category_id = \'' . $cat . '\'' : '';
$date = '(start = end AND start <= \'' . date_aligned(2, time()) . '\' OR start != end AND start <= \'' . date_aligned(2, time()) . '\' AND end >= \'' . date_aligned(2, time()) . '\')';

$news = $db->select('id, start, headline, text, uri', 'news', $date . $cat_field, 'id DESC', POS, CONFIG_ENTRIES);
$c_news = count($news);

if ($c_news > 0) {
	// Überprüfen, ob das Kommentare Modul aktiv ist
	if ($modules->check('comments', 'functions')) {
		$comment_check = true;
		$tpl->assign('comment_check', true);
		include 'modules/comments/functions.php';
	}
	$tpl->assign('pagination', pagination($db->select('id', 'news', $date . $cat_field, 0, 0, 0, 1)));

	for ($i = 0; $i < $c_news; $i++) {
		$news[$i]['date'] = date_aligned(1, $news[$i]['start']);
		$news[$i]['headline'] = $news[$i]['headline'];
		$news[$i]['text'] = $db->escape($news[$i]['text'], 3);
		$news[$i]['uri'] = $db->escape($news[$i]['uri'], 3);
		if (isset($comment_check)) {
			$news[$i]['comments'] = comments_count($news[$i]['id']);
		}
		// HTML-Code entfernen, für den weiterlesen... Link
		$striped_news = strip_tags($news[$i]['text']);
		$striped_news = $db->escape($striped_news, 3);
		$striped_news = html_entity_decode($striped_news, ENT_QUOTES, 'UTF-8');
		$chars = 350;

		// Weiterlesen-Link, falls zusätzliche Links zur News angegeben sind oder Zeichenanzahl größer als $chars
		if (strlen($striped_news) > $chars || $news[$i]['uri'] != '') {
			$striped_news = substr($striped_news, 0, $chars - 50);

			$news[$i]['text'] = $striped_news . '...<a href="' . uri('news/details/id_' . $news[$i]['id']) . '">[' . lang('news', 'read_on') . ']</a>' . "\n";
		}
	}
	$tpl->assign('news', $news);
}

$content = $tpl->fetch('news/list.html');
?>