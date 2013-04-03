<?php
/**
 * Articles
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit;

$period = ' AND (start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)';

if (ACP3_Validate::isNumber(ACP3_CMS::$uri->id) === true &&
	ACP3_CMS::$db2->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'articles WHERE id = :id' . $period, array('id' => ACP3_CMS::$uri->id, 'time' => ACP3_CMS::$date->getCurrentDateTime())) == 1) {
	require_once MODULES_DIR . 'articles/functions.php';

	$page = getArticlesCache(ACP3_CMS::$uri->id);

	ACP3_CMS::$breadcrumb->replaceAnchestor($page['title'], 0, true);

	ACP3_CMS::$view->assign('page', splitTextIntoPages(rewriteInternalUri($page['text']), ACP3_CMS::$uri->getCleanQuery()));
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}