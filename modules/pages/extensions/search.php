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
		$fields = 'title';
		break;
	case 'content':
		$fields = ' text';
		break;
	default:
		$fields = 'title, text';
}
$date = '(start = end AND start <= \'' . dateAligned(2, time()) . '\' OR start != end AND start <= \'' . dateAligned(2, time()) . '\' AND end >= \'' . dateAligned(2, time()) . '\')';

$result_pages = $db->select('id, title, text', 'pages', 'MATCH (' . $fields . ') AGAINST (\'' .  $db->escape($form['search_term']) . '\' IN BOOLEAN MODE) AND mode = \'1\' AND ' . $date, 'id ' . $form['sort']);
$c_result_pages = count($result_pages);

if ($c_result_pages > 0) {
	$results_mods['pages']['title'] = lang('pages', 'pages');
	for ($i = 0; $i < $c_result_pages; ++$i) {
		$results_mods['pages']['results'][$i]['hyperlink'] = uri('pages/list/id_' . $result_pages[$i]['id']);
		$results_mods['pages']['results'][$i]['headline'] = $result_pages[$i]['title'];

		$striped_text = strip_tags($result_pages[$i]['text']);
		$striped_text = $db->escape($striped_text, 3);
		$striped_text = html_entity_decode($striped_text, ENT_QUOTES, 'UTF-8');
		$striped_text = substr($striped_text, 0, 200);

		$results_mods['pages']['results'][$i]['text'] = htmlentities($striped_text, ENT_QUOTES, 'UTF-8') . '...';
	}
}
?>