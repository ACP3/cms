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

$date = '(start = end AND start <= \'' . date_aligned(2, time()) . '\' OR start != end AND start <= \'' . date_aligned(2, time()) . '\' AND end >= \'' . date_aligned(2, time()) . '\')';

switch($form['area']) {
	case 'title':
		$result_dl = $db->select('id, link_title, text', 'dl', 'MATCH (link_title, file) AGAINST (\'' . $db->escape($form['search_term']) . '\' IN BOOLEAN MODE) AND ' . $date, 'start ' . $form['sort'] . ', id ' . $form['sort']);
		break;
	case 'content':
		$result_dl = $db->select('id, link_title, text', 'dl', 'MATCH (text) AGAINST (\'' . $db->escape($form['search_term']) . '\' IN BOOLEAN MODE) AND ' . $date, 'start ' . $form['sort'] . ', id ' . $form['sort']);
		break;
	default:
		$result_dl = $db->select('id, link_title, text', 'dl', 'MATCH (link_title, file, text) AGAINST (\'' . $db->escape($form['search_term']) . '\' IN BOOLEAN MODE) AND ' . $date, 'start ' . $form['sort'] . ', id ' . $form['sort']);
}
$c_result_dl = count($result_dl);

if ($c_result_dl > 0) {
	$i = isset($i) ? $i + 1 : 0;
	$results_mods[$i]['title'] = lang('dl', 'dl');
	for ($j = 0; $j < $c_result_dl; $j++) {
		$results_mods[$i]['results'][$j]['hyperlink'] = uri('dl/details/id_' . $result_dl[$i]['id']);
		$results_mods[$i]['results'][$j]['headline'] = $result_dl[$i]['link_title'];

		$striped_text = strip_tags($result_dl[$i]['text']);
		$striped_text = $db->escape($striped_text, 3);
		$striped_text = html_entity_decode($striped_text, ENT_QUOTES, CHARSET);
		$striped_text = substr($striped_text, 0, 200);

		$results_mods[$i]['results'][$j]['text'] = htmlentities($striped_text, ENT_QUOTES, CHARSET) . '...';
	}
}
?>