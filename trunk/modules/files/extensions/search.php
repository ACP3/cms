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
		$fields = 'link_title, file';
		break;
	case 'content':
		$fields = 'text';
		break;
	default:
		$fields = 'link_title, file, text';
}

$time = $date->timestamp();
$period = '(start = end AND start <= ' . $time . ' OR start != end AND start <= ' . $time . ' AND end >= ' . $time . ')';

$result_files = $db->select('id, link_title, text', 'files', 'MATCH (' . $fields . ') AGAINST (\'' . $db->escape($_POST['search_term']) . '\' IN BOOLEAN MODE) AND ' . $period, 'start ' . $_POST['sort'] . ', end ' . $_POST['sort'] . ', id ' . $_POST['sort']);
$c_result_files = count($result_files);

if ($c_result_files > 0) {
	$results_mods['files']['title'] = $lang->t('files', 'files');
	for ($i = 0; $i < $c_result_files; ++$i) {
		$results_mods['files']['results'][$i]['hyperlink'] = $uri->route('files/details/id_' . $result_files[$i]['id'], 1);
		$results_mods['files']['results'][$i]['headline'] = $db->escape($result_files[$i]['link_title'], 3);
		$results_mods['files']['results'][$i]['text'] = shortenEntry($db->escape($result_files[$i]['text'], 3), 200, 0, '...');
	}
}
