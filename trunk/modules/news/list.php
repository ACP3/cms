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

if (isset($_POST['form']['cat']) && validate::isNumber($_POST['form']['cat'])) {
	$cat = $_POST['form']['cat'];
} elseif (validate::isNumber($uri->cat)) {
	$cat = $uri->cat;
} else {
	$cat = 0;
}

// Kategorien auflisten
if (modules::check('categories', 'functions')) {
	include_once ACP3_ROOT . 'modules/categories/functions.php';
	$categories = categoriesList('news', 'list', $cat);

	$tpl->assign('categories', $categories);
}

// Falls Kategorie angegeben, News nur aus eben jener selektieren
$cat = !empty($cat) ? ' AND category_id = \'' . $cat . '\'' : '';
$where = '(start = end AND start <= \'' . $date->timestamp() . '\' OR start != end AND start <= \'' . $date->timestamp() . '\' AND end >= \'' . $date->timestamp() . '\')' . $cat;

$news = $db->select('id, start, headline, text, readmore, comments, uri', 'news', $where, 'start DESC, end DESC, id DESC', POS, CONFIG_ENTRIES);
$c_news = count($news);

if ($c_news > 0) {
	// Überprüfen, ob das Kommentare Modul aktiv ist
	if (modules::check('comments', 'functions')) {
		include_once ACP3_ROOT . 'modules/comments/functions.php';
		$comment_check = true;
	}
	$tpl->assign('pagination', pagination($db->select('id', 'news', $where, 0, 0, 0, 1)));

	for ($i = 0; $i < $c_news; ++$i) {
		$news[$i]['date'] = $date->format($news[$i]['start']);
		$news[$i]['headline'] = $news[$i]['headline'];
		$news[$i]['text'] = $db->escape($news[$i]['text'], 3);
		$news[$i]['uri'] = $db->escape($news[$i]['uri'], 3);
		$news[$i]['allow_comments'] = false;
		if ($news[$i]['comments'] == '1' && isset($comment_check)) {
			$news[$i]['comments_count'] = commentsCount($news[$i]['id']);
			$news[$i]['allow_comments'] = true;
		}
		if ($news[$i]['readmore'] == '1') {
			// HTML-Code für den "weiterlesen..." Link entfernen
			$striped_news = strip_tags($news[$i]['text']);
			$striped_news = $db->escape($striped_news, 3);
			$striped_news = html_entity_decode($striped_news, ENT_QUOTES, 'UTF-8');
			$chars = 350;

			// Weiterlesen-Link, falls zusätzliche Links zur News angegeben sind oder Zeichenanzahl größer als $chars
			if (strlen($striped_news) - $chars >= 50 || $news[$i]['uri'] != '') {
				$striped_news = substr($striped_news, 0, $chars - 50);

				$news[$i]['text'] = $striped_news . '...<a href="' . uri('news/details/id_' . $news[$i]['id']) . '">[' . $lang->t('news', 'read_on') . ']</a>' . "\n";
			}
		}
	}
	$tpl->assign('news', $news);
}

$content = $tpl->fetch('news/list.html');
?>