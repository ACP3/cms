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
		$result_pages = $db->select('id, title, text', 'pages', 'MATCH (title) AGAINST (\'' .  $db->escape($form['search_term']) . '\' IN BOOLEAN MODE) AND mode = \'1\'', 'id ' . $form['sort']);
		break;
	case 'content':
		$result_pages = $db->select('id, title, text', 'pages', 'MATCH (text) AGAINST (\'' .  $db->escape($form['search_term']) . '\' IN BOOLEAN MODE) AND mode = \'1\'', 'id ' . $form['sort']);
		break;
	default:
		$result_pages = $db->select('id, title, text', 'pages', 'MATCH (title, text) AGAINST (\'' .  $db->escape($form['search_term']) . '\' IN BOOLEAN MODE) AND mode = \'1\'', 'id ' . $form['sort']);
}
$c_result_pages = $validate->countArrayElements($result_pages);

if ($c_result_pages > 0) {
	$i = isset($i) ? $i + 1 : 0;
	$results_mods[$i]['title'] = lang('pages', 'pages');
	for ($j = 0; $j < $c_result_pages; $j++) {
		$results_mods[$i]['results'][$j]['hyperlink'] = uri('pages/list/id_' . $result_pages[$j]['id']);
		$results_mods[$i]['results'][$j]['headline'] = $result_pages[$j]['title'];

		$striped_text = strip_tags($result_pages[$j]['text']);
		$striped_text = $db->escape($striped_text, 3);
		$striped_text = html_entity_decode($striped_text, ENT_QUOTES, 'UTF-8');
		$striped_text = substr($striped_text, 0, 200);

		$results_mods[$i]['results'][$j]['text'] = htmlentities($striped_text, ENT_QUOTES, 'UTF-8') . '...';
	}
}
?>