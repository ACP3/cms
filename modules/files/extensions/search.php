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
		$fields = 'link_title, file';
		break;
	case 'content':
		$fields = 'text';
		break;
	default:
		$fields = 'link_title, file, text';
}

$time = ACP3_CMS::$date->getCurrentDateTime();
$period = '(start = end AND start <= \'' . $time . '\' OR start != end AND start <= \'' . $time . '\' AND end >= \'' . $time . '\')';

$result_files = ACP3_CMS::$db->select('id, link_title, text', 'files', 'MATCH (' . $fields . ') AGAINST (\'' . ACP3_CMS::$db->escape($_POST['search_term']) . '\' IN BOOLEAN MODE) AND ' . $period, 'start ' . $_POST['sort'] . ', end ' . $_POST['sort'] . ', id ' . $_POST['sort']);
$c_result_files = count($result_files);

if ($c_result_files > 0) {
	$results_mods['files']['title'] = ACP3_CMS::$lang->t('files', 'files');
	for ($i = 0; $i < $c_result_files; ++$i) {
		$results_mods['files']['results'][$i]['hyperlink'] = ACP3_CMS::$uri->route('files/details/id_' . $result_files[$i]['id'], 1);
		$results_mods['files']['results'][$i]['headline'] = ACP3_CMS::$db->escape($result_files[$i]['link_title'], 3);
		$results_mods['files']['results'][$i]['text'] = shortenEntry(ACP3_CMS::$db->escape($result_files[$i]['text'], 3), 200, 0, '...');
	}
}
