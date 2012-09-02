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

$time = ACP3_CMS::$date->getCurrentDateTime();
$period = ' AND (start = end AND start <= \'' . $time . '\' OR start != end AND start <= \'' . $time . '\' AND end >= \'' . $time . '\')';

if (ACP3_Validate::isNumber(ACP3_CMS::$uri->id) === true && ACP3_CMS::$db->countRows('*', 'news', 'id = \'' . ACP3_CMS::$uri->id . '\'' . $period) == 1) {
	require_once MODULES_DIR . 'news/functions.php';

	$settings = ACP3_Config::getSettings('news');
	$news = getNewsCache(ACP3_CMS::$uri->id);
	$news[0]['headline'] = ACP3_CMS::$db->escape($news[0]['headline'], 3);

	ACP3_CMS::$breadcrumb->append(ACP3_CMS::$lang->t('news', 'news'), ACP3_CMS::$uri->route('news'));
	if ($settings['category_in_breadcrumb'] == 1) {
		// Brotkrümelspur
		$category = ACP3_CMS::$db->select('name', 'categories', 'id = \'' . $news[0]['category_id'] . '\'');
		if (count($category) > 0) {
			ACP3_CMS::$breadcrumb->append($category[0]['name'], ACP3_CMS::$uri->route('news/list/cat_' . $news[0]['category_id']));
		}
	}
	ACP3_CMS::$breadcrumb->append($news[0]['headline']);

	$news[0]['date'] = ACP3_CMS::$date->format($news[0]['start'], $settings['dateformat']);
	$news[0]['text'] = rewriteInternalUri(ACP3_CMS::$db->escape($news[0]['text'], 3));
	$news[0]['uri'] = ACP3_CMS::$db->escape($news[0]['uri'], 3);
	if (!empty($news[0]['uri']) && strpos($news[0]['uri'], 'http://') === false) {
		$news[0]['uri'] = 'http://' . $news[0]['uri'];
	}
	$news[0]['target'] = $news[0]['target'] == 2 ? ' onclick="window.open(this.href); return false"' : '';

	ACP3_CMS::$view->assign('news', $news[0]);

	if ($settings['comments'] == 1 && $news[0]['comments'] == 1 && ACP3_Modules::check('comments', 'functions') === true) {
		require_once MODULES_DIR . 'comments/functions.php';

		ACP3_CMS::$view->assign('comments', commentsList('news', ACP3_CMS::$uri->id));
	}
	ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('news/details.tpl'));
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}