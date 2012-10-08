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

switch($_POST['area']) {
	case 'title':
		$fields = 'title';
		break;
	case 'content':
		$fields = 'text';
		break;
	default:
		$fields = 'title, text';
}

$period = '(start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)';
$result_pages = ACP3_CMS::$db2->fetchAll('SELECT id, title, text FROM ' . DB_PRE . 'articles WHERE MATCH (' . $fields . ') AGAINST (' . ACP3_CMS::$db2->quote($_POST['search_term']) . ' IN BOOLEAN MODE) AND ' . $period . 'ORDER BY start ' . $_POST['sort'] . ', end ' . $_POST['sort'] . ', title ' . $_POST['sort'], array('search_term' => $_POST['search_term'], 'time' => ACP3_CMS::$date->getCurrentDateTime()));
$c_result_pages = count($result_pages);

if ($c_result_pages > 0) {
	$name =  ACP3_CMS::$lang->t('articles', 'articles');
	$results_mods[$name]['dir'] = 'articles';
	for ($i = 0; $i < $c_result_pages; ++$i) {
		$results_mods[$name]['results'][$i]['hyperlink'] = ACP3_CMS::$uri->route('articles/list/id_' . $result_pages[$i]['id']);
		$results_mods[$name]['results'][$i]['title'] = $result_pages[$i]['title'];
		$results_mods[$name]['results'][$i]['text'] = shortenEntry($result_pages[$i]['text'], 200, 0, '...');
	}
}
