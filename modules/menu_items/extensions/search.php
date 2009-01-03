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
		$fields = ' uri';
		break;
	default:
		$fields = 'title, uri';
}
$time = $date->timestamp();
$period = '(start = end AND start <= \'' . $time . '\' OR start != end AND start <= \'' . $time . '\' AND end >= \'' . $time . '\')';

$result_pages = $db->select('id, mode, title, uri', 'menu_items', 'MATCH (' . $fields . ') AGAINST (\'' .  $db->escape($form['search_term']) . '\' IN BOOLEAN MODE) AND mode = \'1\' AND ' . $period, 'start ' . $form['sort'] . ', end ' . $form['sort'] . ', id ' . $form['sort']);
$c_result_pages = count($result_pages);

if ($c_result_pages > 0) {
	$results_mods['pages']['title'] = $lang->t('menu_items', 'menu_items');
	for ($i = 0; $i < $c_result_pages; ++$i) {
		$results_mods['pages']['results'][$i]['hyperlink'] = $result_pages[$i]['uri'];
		$results_mods['pages']['results'][$i]['headline'] = $result_pages[$i]['title'];
		$results_mods['pages']['results'][$i]['text'] = '';
	}
}
?>