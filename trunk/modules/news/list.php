<?php
/**
 * News
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit;

if (isset($_POST['form']['cat']) && validate::isNumber($_POST['form']['cat'])) {
	$cat = $_POST['form']['cat'];
} elseif (validate::isNumber($uri->cat)) {
	$cat = $uri->cat;
} else {
	$cat = 0;
}

// Kategorien auflisten
if (modules::check('categories', 'functions') == 1) {
	require_once MODULES_DIR . 'categories/functions.php';
	$tpl->assign('categories', categoriesList('news', $cat));
}

// Falls Kategorie angegeben, News nur aus eben jener selektieren
$cat = !empty($cat) ? ' AND category_id = \'' . $cat . '\'' : '';
$time = $date->timestamp();
$where = '(start = end AND start <= \'' . $time . '\' OR start != end AND start <= \'' . $time . '\' AND end >= \'' . $time . '\')' . $cat;

$news = $db->select('id, start, headline, text, readmore, comments, uri', 'news', $where, 'start DESC, end DESC, id DESC', POS, $auth->entries);
$c_news = count($news);

if ($c_news > 0) {
	// Überprüfen, ob das Kommentare Modul aktiv ist
	if (modules::check('comments', 'functions') == 1) {
		require_once MODULES_DIR . 'comments/functions.php';
		$comment_check = true;
	}
	$tpl->assign('pagination', pagination($db->countRows('*', 'news', $where)));

	$settings = config::getModuleSettings('news');

	for ($i = 0; $i < $c_news; ++$i) {
		$news[$i]['date'] = $date->format($news[$i]['start'], $settings['dateformat']);
		$news[$i]['headline'] = $db->escape($news[$i]['headline'], 3);
		$news[$i]['text'] = rewriteInternalUri($db->escape($news[$i]['text'], 3));
		$news[$i]['uri'] = $db->escape($news[$i]['uri'], 3);
		$news[$i]['allow_comments'] = false;
		if ($settings['comments'] == 1 && $news[$i]['comments'] == 1 && isset($comment_check)) {
			$news[$i]['comments'] = commentsCount('news', $news[$i]['id']);
			$news[$i]['allow_comments'] = true;
		}
		if ($settings['readmore'] == 1 && $news[$i]['readmore'] == 1) {
			$news[$i]['text'] = shortenEntry($news[$i]['text'], $settings['readmore_chars'], 50, '...<a href="' . $uri->route('news/details/id_' . $news[$i]['id'], 1) . '">[' . $lang->t('news', 'readmore') . "]</a>\n");
		}
	}
	$tpl->assign('news', $news);
}

$content = modules::fetchTemplate('news/list.html');