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

$time = ACP3_CMS::$date->getCurrentDateTime();
$period = ' AND (start = end AND start <= \'' . $time . '\' OR start != end AND start <= \'' . $time . '\' AND end >= \'' . $time . '\')';

if (ACP3_Validate::isNumber(ACP3_CMS::$uri->id) === true && ACP3_CMS::$db->countRows('*', 'articles', 'id = \'' . ACP3_CMS::$uri->id . '\'' . $period) == 1) {
	require_once MODULES_DIR . 'articles/functions.php';

	$page = getArticlesCache(ACP3_CMS::$uri->id);

	ACP3_CMS::$breadcrumb->replaceAnchestor(ACP3_CMS::$db->escape($page[0]['title'], 3));

	ACP3_CMS::$view->assign('page', splitTextIntoPages(rewriteInternalUri(ACP3_CMS::$db->escape($page[0]['text'], 3)), ACP3_CMS::$uri->getCleanQuery()));
	ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('articles/list.tpl'));
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}