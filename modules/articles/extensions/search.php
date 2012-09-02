<?php
/**
 * Search
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
$time = ACP3_CMS::$date->getCurrentDateTime();
$period = '(start = end AND start <= \'' . $time . '\' OR start != end AND start <= \'' . $time . '\' AND end >= \'' . $time . '\')';

$result_pages = ACP3_CMS::$db->select('id, title, text', 'articles', 'MATCH (' . $fields . ') AGAINST (\'' .  ACP3_CMS::$db->escape($_POST['search_term']) . '\' IN BOOLEAN MODE) AND ' . $period, 'start ' . $_POST['sort'] . ', end ' . $_POST['sort'] . ', title ' . $_POST['sort']);
$c_result_pages = count($result_pages);

if ($c_result_pages > 0) {
	$results_mods['articles']['title'] = ACP3_CMS::$lang->t('articles', 'articles');
	for ($i = 0; $i < $c_result_pages; ++$i) {
		$results_mods['articles']['results'][$i]['hyperlink'] = ACP3_CMS::$uri->route('articles/list/id_' . $result_pages[$i]['id'], 1);
		$results_mods['articles']['results'][$i]['headline'] = ACP3_CMS::$db->escape($result_pages[$i]['title'], 3);
		$results_mods['articles']['results'][$i]['text'] = shortenEntry(ACP3_CMS::$db->escape($result_pages[$i]['text'], 3), 200, 0, '...');
	}
}
