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

if (validate::isNumber($uri->id) === true && $db->countRows('*', 'static_pages', 'id = \'' . $uri->id . '\'' . $period) == 1) {
	require_once MODULES_DIR . 'static_pages/functions.php';

	$page = getStaticPagesCache($uri->id);

	$breadcrumb->replaceAnchestor($db->escape($page[0]['title'], 3));

	// Text der Seite parsen
	$page[0]['text'] = rewriteInternalUri($db->escape($page[0]['text'], 3));

	// Falls keine Seitenumbrüche vorhanden sein sollten, Text nicht unnötig bearbeiten
	if (strpos($page[0]['text'], 'class="page-break"') === false) {
		$tpl->assign('text', $page[0]['text']);
	} else {
		$regex = '/<hr(.+)class="page-break"(.*)(\/>|>)/iU';

		$matches = array();
		preg_match_all($regex, $page[0]['text'], $matches);

		$pages = preg_split($regex, $page[0]['text'], -1, PREG_SPLIT_NO_EMPTY);
		$c_pages = count($pages);
		$currentPage = validate::isNumber($uri->page) === true && $uri->page <= $c_pages ? $uri->page - 1 : 0;
		$path = $uri->getCleanQuery();

		$page = array(
			'toc' => generateTOC($matches[0], $path),
			'text' => $pages[$currentPage],
			'next' => $currentPage + 2 <= $c_pages ? $uri->route($path, 1) . 'page_' . ($currentPage + 2) . '/' : '',
			'previous' => $currentPage > 0 ? $uri->route($path, 1) . 'page_' . $currentPage . '/' : '',
		);

		$tpl->assign('page', $page);
	}
	view::setContent(view::fetchTemplate('static_pages/list.tpl'));
} else {
	$uri->redirect('errors/404');
}