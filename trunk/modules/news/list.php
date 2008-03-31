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

// Newsletter
$tpl->assign('MOD_newsletter', $modules->check('newsletter', 'create'));
// RSS Feed Icon
$tpl->assign('MOD_feeds', $modules->check('feeds', 'list'));

if (isset($_POST['form']['cat']) && $validate->isNumber($_POST['form']['cat'])) {
	$cat = $_POST['form']['cat'];
} elseif ($validate->isNumber($modules->cat)) {
	$cat = $modules->cat;
} else {
	$cat = 0;
}

// Kategorien auflisten
if ($modules->check('categories', 'functions')) {
	include_once ACP3_ROOT . 'modules/categories/functions.php';
	$categories = categoriesList('news', 'list', $cat);

	$tpl->assign('categories', $categories);
}

// Falls Kategorie angegeben, News nur aus eben jener selektieren
$cat = !empty($cat) ? ' AND category_id = \'' . $cat . '\'' : '';
$where = '(start = end AND start <= \'' . dateAligned(2, time()) . '\' OR start != end AND start <= \'' . dateAligned(2, time()) . '\' AND end >= \'' . dateAligned(2, time()) . '\')' . $cat;

$news = $db->select('id, start, headline, text, uri', 'news', $where, 'id DESC', POS, CONFIG_ENTRIES);
$c_news = $validate->countArrayElements($news);

if ($c_news > 0) {
	// Überprüfen, ob das Kommentare Modul aktiv ist
	if ($modules->check('comments', 'functions')) {
		include_once ACP3_ROOT . 'modules/comments/functions.php';
		$comment_check = true;
		$tpl->assign('comment_check', true);
	}
	$tpl->assign('pagination', $modules->pagination($db->select('id', 'news', $where, 0, 0, 0, 1)));

	for ($i = 0; $i < $c_news; $i++) {
		$news[$i]['date'] = dateAligned(1, $news[$i]['start']);
		$news[$i]['headline'] = $news[$i]['headline'];
		$news[$i]['text'] = $db->escape($news[$i]['text'], 3);
		$news[$i]['uri'] = $db->escape($news[$i]['uri'], 3);
		if (isset($comment_check)) {
			$news[$i]['comments'] = commentsCount($news[$i]['id']);
		}
		// HTML-Code für den "weiterlesen..." Link entfernen
		$striped_news = strip_tags($news[$i]['text']);
		$striped_news = $db->escape($striped_news, 3);
		$striped_news = html_entity_decode($striped_news, ENT_QUOTES, 'UTF-8');
		$chars = 350;

		// Weiterlesen-Link, falls zusätzliche Links zur News angegeben sind oder Zeichenanzahl größer als $chars
		if (strlen($striped_news) - $chars >= 50 || $news[$i]['uri'] != '') {
			$striped_news = substr($striped_news, 0, $chars - 50);

			$news[$i]['text'] = $striped_news . '...<a href="' . uri('news/details/id_' . $news[$i]['id']) . '">[' . lang('news', 'read_on') . ']</a>' . "\n";
		}
	}
	$tpl->assign('news', $news);
}

$content = $tpl->fetch('news/list.html');
?>