<?php
/**
 * Search
 *
 * @author Goratsch Webdesign
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
$time = $date->timestamp();
$period = '(start = end AND start <= ' . $time . ' OR start != end AND start <= ' . $time . ' AND end >= ' . $time . ')';

$result_pages = $db->select('id, title, text', 'static_pages', 'MATCH (' . $fields . ') AGAINST (\'' .  $db->escape($_POST['search_term']) . '\' IN BOOLEAN MODE) AND ' . $period, 'start ' . $_POST['sort'] . ', end ' . $_POST['sort'] . ', title ' . $_POST['sort']);
$c_result_pages = count($result_pages);

if ($c_result_pages > 0) {
	$results_mods['static_pages']['title'] = $lang->t('static_pages', 'static_pages');
	for ($i = 0; $i < $c_result_pages; ++$i) {
		$results_mods['static_pages']['results'][$i]['hyperlink'] = $uri->route('static_pages/list/id_' . $result_pages[$i]['id'], 1);
		$results_mods['static_pages']['results'][$i]['headline'] = $db->escape($result_pages[$i]['title'], 3);
		$results_mods['static_pages']['results'][$i]['text'] = shortenEntry($db->escape($result_pages[$i]['text'], 3), 200, 0, '...');
	}
}
