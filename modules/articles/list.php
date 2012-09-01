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

$time = $date->getCurrentDateTime();
$period = ' AND (start = end AND start <= \'' . $time . '\' OR start != end AND start <= \'' . $time . '\' AND end >= \'' . $time . '\')';

if (ACP3_Validate::isNumber($uri->id) === true && $db->countRows('*', 'articles', 'id = \'' . $uri->id . '\'' . $period) == 1) {
	require_once MODULES_DIR . 'articles/functions.php';

	$page = getArticlesCache($uri->id);

	$breadcrumb->replaceAnchestor($db->escape($page[0]['title'], 3));

	$tpl->assign('page', splitTextIntoPages(rewriteInternalUri($db->escape($page[0]['text'], 3)), $uri->getCleanQuery()));
	ACP3_View::setContent(ACP3_View::fetchTemplate('articles/list.tpl'));
} else {
	$uri->redirect('errors/404');
}