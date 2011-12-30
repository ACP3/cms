<?php
/**
 * Static Pages
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit;

$time = $date->timestamp();
$period = ' AND (start = end AND start <= \'' . $time . '\' OR start != end AND start <= \'' . $time . '\' AND end >= \'' . $time . '\')';

if (validate::isNumber($uri->id) && $db->countRows('*', 'static_pages', 'id = \'' . $uri->id . '\'' . $period) == 1) {
	require_once MODULES_DIR . 'static_pages/functions.php';

	$page = getStaticPagesCache($uri->id);

	breadcrumb::assign($db->escape($page[0]['title'], 3));

	$page[0]['text'] = rewriteInternalUri($db->escape($page[0]['text'], 3));
	$tpl->assign('text', $page[0]['text']);
	$content = modules::fetchTemplate('static_pages/list.html');
} else {
	$uri->redirect('errors/404');
}