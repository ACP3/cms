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

$time = $date->getCurrentDateTime();
$period = ' AND (start = end AND start <= \'' . $time . '\' OR start != end AND start <= \'' . $time . '\' AND end >= \'' . $time . '\')';

if (ACP3_Validate::isNumber($uri->id) === true && $db->countRows('*', 'news', 'id = \'' . $uri->id . '\'' . $period) == 1) {
	require_once MODULES_DIR . 'news/functions.php';

	$settings = ACP3_Config::getSettings('news');
	$news = getNewsCache($uri->id);
	$news[0]['headline'] = $db->escape($news[0]['headline'], 3);

	$breadcrumb->append($lang->t('news', 'news'), $uri->route('news'));
	if ($settings['category_in_breadcrumb'] == 1) {
		// Brotkrümelspur
		$category = $db->select('name', 'categories', 'id = \'' . $news[0]['category_id'] . '\'');
		if (count($category) > 0) {
			$breadcrumb->append($category[0]['name'], $uri->route('news/list/cat_' . $news[0]['category_id']));
		}
	}
	$breadcrumb->append($news[0]['headline']);

	$news[0]['date'] = $date->format($news[0]['start'], $settings['dateformat']);
	$news[0]['text'] = rewriteInternalUri($db->escape($news[0]['text'], 3));
	$news[0]['uri'] = $db->escape($news[0]['uri'], 3);
	if (!empty($news[0]['uri']) && strpos($news[0]['uri'], 'http://') === false) {
		$news[0]['uri'] = 'http://' . $news[0]['uri'];
	}
	$news[0]['target'] = $news[0]['target'] == 2 ? ' onclick="window.open(this.href); return false"' : '';

	$tpl->assign('news', $news[0]);

	if ($settings['comments'] == 1 && $news[0]['comments'] == 1 && ACP3_Modules::check('comments', 'functions') === true) {
		require_once MODULES_DIR . 'comments/functions.php';

		$tpl->assign('comments', commentsList('news', $uri->id));
	}
	ACP3_View::setContent(ACP3_View::fetchTemplate('news/details.tpl'));
} else {
	$uri->redirect('errors/404');
}