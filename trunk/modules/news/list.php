<?php
/**
 * News
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit;

if (isset($_POST['cat']) && ACP3_Validate::isNumber($_POST['cat']) === true) {
	$cat = (int) $_POST['cat'];
} elseif (ACP3_Validate::isNumber(ACP3_CMS::$uri->cat) === true) {
	$cat = (int) ACP3_CMS::$uri->cat;
} else {
	$cat = 0;
}

if (ACP3_Modules::check('categories', 'functions') === true) {
	require_once MODULES_DIR . 'categories/functions.php';
	ACP3_CMS::$view->assign('categories', categoriesList('news', $cat));
}

$settings = ACP3_Config::getSettings('news');
// Kategorie in Brotkrümelspur anzeigen
if ($cat !== 0 && $settings['category_in_breadcrumb'] == 1) {
	ACP3_CMS::$breadcrumb->append(ACP3_CMS::$lang->t('news', 'news'), ACP3_CMS::$uri->route('news'));
	$category = ACP3_CMS::$db->select('name', 'categories', 'id = \'' . $cat . '\'');
	if (count($category) > 0) {
		ACP3_CMS::$breadcrumb->append($category[0]['name']);
	}
}

// Falls Kategorie angegeben, News nur aus eben jener selektieren
$cat = !empty($cat) ? ' AND category_id = ' . $cat : '';
$time = ACP3_CMS::$date->getCurrentDateTime();
$where = '(start = end AND start <= \'' . $time . '\' OR start != end AND start <= \'' . $time . '\' AND end >= \'' . $time . '\')' . $cat;

$news = ACP3_CMS::$db->select('id, start, headline, text, readmore, comments, uri', 'news', $where, 'start DESC, end DESC, id DESC', POS, ACP3_CMS::$auth->entries);
$c_news = count($news);

if ($c_news > 0) {
	// Überprüfen, ob das Kommentare Modul aktiv ist
	if (ACP3_Modules::check('comments', 'functions') === true) {
		require_once MODULES_DIR . 'comments/functions.php';
		$comment_check = true;
	}

	ACP3_CMS::$view->assign('pagination', pagination(ACP3_CMS::$db->countRows('*', 'news', $where)));

	for ($i = 0; $i < $c_news; ++$i) {
		$news[$i]['date'] = ACP3_CMS::$date->format($news[$i]['start'], $settings['dateformat']);
		$news[$i]['headline'] = ACP3_CMS::$db->escape($news[$i]['headline'], 3);
		$news[$i]['text'] = rewriteInternalUri(ACP3_CMS::$db->escape($news[$i]['text'], 3));
		$news[$i]['uri'] = ACP3_CMS::$db->escape($news[$i]['uri'], 3);
		$news[$i]['allow_comments'] = false;
		if ($settings['comments'] == 1 && $news[$i]['comments'] == 1 && isset($comment_check)) {
			$news[$i]['comments'] = commentsCount('news', $news[$i]['id']);
			$news[$i]['allow_comments'] = true;
		}
		if ($settings['readmore'] == 1 && $news[$i]['readmore'] == 1) {
			$news[$i]['text'] = shortenEntry($news[$i]['text'], $settings['readmore_chars'], 50, '...<a href="' . ACP3_CMS::$uri->route('news/details/id_' . $news[$i]['id'], 1) . '">[' . ACP3_CMS::$lang->t('news', 'readmore') . "]</a>\n");
		}
	}
	ACP3_CMS::$view->assign('news', $news);
}

ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('news/list.tpl'));