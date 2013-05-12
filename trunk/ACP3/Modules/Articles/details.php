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

if (ACP3\Core\Validate::isNumber(ACP3\CMS::$injector['URI']->id) === true &&
	ACP3\CMS::$injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'articles WHERE id = :id' . $period, array('id' => ACP3\CMS::$injector['URI']->id, 'time' => ACP3\CMS::$injector['Date']->getCurrentDateTime())) == 1) {
	require_once MODULES_DIR . 'articles/functions.php';

	$page = getArticlesCache(ACP3\CMS::$injector['URI']->id);

	ACP3\CMS::$injector['Breadcrumb']->replaceAnchestor($page['title'], 0, true);

	ACP3\CMS::$injector['View']->assign('page', splitTextIntoPages(ACP3\Core\Functions::rewriteInternalUri($page['text']), ACP3\CMS::$injector['URI']->getCleanQuery()));
} else {
	ACP3\CMS::$injector['URI']->redirect('errors/404');
}