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

$period = ' AND (start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)';
if (ACP3_Validate::isNumber(ACP3_CMS::$uri->id) === true &&
	ACP3_CMS::$db2->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'news WHERE id = :id' . $period, array('id' => ACP3_CMS::$uri->id, 'time' => ACP3_CMS::$date->getCurrentDateTime())) == 1) {
	require_once MODULES_DIR . 'news/functions.php';

	$settings = ACP3_Config::getSettings('news');
	$news = getNewsCache(ACP3_CMS::$uri->id);

	ACP3_CMS::$breadcrumb->append(ACP3_CMS::$lang->t('news', 'news'), ACP3_CMS::$uri->route('news'));
	if ($settings['category_in_breadcrumb'] == 1) {
		// Brotkrümelspur
		$category = ACP3_CMS::$db2->fetchColumn('SELECT title FROM ' . DB_PRE . 'categories WHERE id = ?', array($news['category_id']));
		if (!empty($category)) {
			ACP3_CMS::$breadcrumb->append($category, ACP3_CMS::$uri->route('news/list/cat_' . $news['category_id']));
		}
	}
	ACP3_CMS::$breadcrumb->append($news['title']);

	$news['date'] = ACP3_CMS::$date->format($news['start'], $settings['dateformat']);
	$news['text'] = rewriteInternalUri($news['text']);
	if (!empty($news['uri']) && (bool) preg_match('=^http(s)?://=', $news['uri']) === false) {
		$news['uri'] = 'http://' . $news['uri'];
	}
	$news['target'] = $news['target'] == 2 ? ' onclick="window.open(this.href); return false"' : '';

	ACP3_CMS::$view->assign('news', $news);

	if ($settings['comments'] == 1 && $news['comments'] == 1 && ACP3_Modules::check('comments', 'functions') === true) {
		require_once MODULES_DIR . 'comments/functions.php';

		ACP3_CMS::$view->assign('comments', commentsList('news', ACP3_CMS::$uri->id));
	}
	ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('news/details.tpl'));
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}