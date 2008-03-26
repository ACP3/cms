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

$date = '(start = end AND start <= \'' . dateAligned(2, time()) . '\' OR start != end AND start <= \'' . dateAligned(2, time()) . '\' AND end >= \'' . dateAligned(2, time()) . '\')';

switch($form['area']) {
	case 'title':
		$result_files = $db->select('id, link_title, text', 'files', 'MATCH (link_title, file) AGAINST (\'' . $db->escape($form['search_term']) . '\' IN BOOLEAN MODE) AND ' . $date, 'start ' . $form['sort'] . ', id ' . $form['sort']);
		break;
	case 'content':
		$result_files = $db->select('id, link_title, text', 'files', 'MATCH (text) AGAINST (\'' . $db->escape($form['search_term']) . '\' IN BOOLEAN MODE) AND ' . $date, 'start ' . $form['sort'] . ', id ' . $form['sort']);
		break;
	default:
		$result_files = $db->select('id, link_title, text', 'files', 'MATCH (link_title, file, text) AGAINST (\'' . $db->escape($form['search_term']) . '\' IN BOOLEAN MODE) AND ' . $date, 'start ' . $form['sort'] . ', id ' . $form['sort']);
}
$c_result_files = count($result_files);

if ($c_result_files > 0) {
	$i = isset($i) ? $i + 1 : 0;
	$results_mods[$i]['title'] = lang('files', 'files');
	for ($j = 0; $j < $c_result_files; $j++) {
		$results_mods[$i]['results'][$j]['hyperlink'] = uri('files/details/id_' . $result_files[$j]['id']);
		$results_mods[$i]['results'][$j]['headline'] = $result_files[$j]['link_title'];

		$striped_text = strip_tags($result_files[$j]['text']);
		$striped_text = $db->escape($striped_text, 3);
		$striped_text = html_entity_decode($striped_text, ENT_QUOTES, 'UTF-8');
		$striped_text = substr($striped_text, 0, 200);

		$results_mods[$i]['results'][$j]['text'] = htmlentities($striped_text, ENT_QUOTES, 'UTF-8') . '...';
	}
}
?>