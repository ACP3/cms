<?php
/**
 * Search
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3'))
	exit;

switch($form['area']) {
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
$period = '(start = end AND start <= \'' . $time . '\' OR start != end AND start <= \'' . $time . '\' AND end >= \'' . $time . '\')';

$result_files = $db->select('id, link_title, text', 'files', 'MATCH (' . $fields . ') AGAINST (\'' . $db->escape($form['search_term']) . '\' IN BOOLEAN MODE) AND ' . $period, 'start ' . $form['sort'] . ', end ' . $form['sort'] . ', id ' . $form['sort']);
$c_result_files = count($result_files);

if ($c_result_files > 0) {
	$results_mods['files']['title'] = $lang->t('files', 'files');
	for ($i = 0; $i < $c_result_files; ++$i) {
		$results_mods['files']['results'][$i]['hyperlink'] = uri('files/details/id_' . $result_files[$i]['id']);
		$results_mods['files']['results'][$i]['headline'] = $result_files[$i]['link_title'];

		$striped_text = strip_tags($result_files[$i]['text']);
		$striped_text = $db->escape($striped_text, 3);
		$striped_text = html_entity_decode($striped_text, ENT_QUOTES, 'UTF-8');
		$striped_text = substr($striped_text, 0, 200);

		$results_mods['files']['results'][$i]['text'] = htmlentities($striped_text, ENT_QUOTES, 'UTF-8') . '...';
	}
}
?>