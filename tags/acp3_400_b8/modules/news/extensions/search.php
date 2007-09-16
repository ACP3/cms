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
		$result_news = $db->select('id, headline, text', 'news', 'MATCH (headline) AGAINST (\'' . $db->escape($form['search_term']) . '\' IN BOOLEAN MODE) AND ' . $date, 'start ' . $form['sort'] . ', id ' . $form['sort']);
		break;
	case 'content':
		$result_news = $db->select('id, headline, text', 'news', 'MATCH (text) AGAINST (\'' . $db->escape($form['search_term']) . '\' IN BOOLEAN MODE) AND ' . $date, 'start ' . $form['sort'] . ', id ' . $form['sort']);
		break;
	default:
		$result_news = $db->select('id, headline, text', 'news', 'MATCH (headline, text) AGAINST (\'' . $db->escape($form['search_term']) . '\' IN BOOLEAN MODE) AND ' . $date, 'start ' . $form['sort'] . ', id ' . $form['sort']);
}
$c_result_news = count($result_news);

if ($c_result_news > 0) {
	$i = isset($i) ? $i + 1 : 0;
	$results_mods[$i]['title'] = lang('news', 'news');
	for ($j = 0; $j < $c_result_news; $j++) {
		$results_mods[$i]['results'][$j]['hyperlink'] = uri('news/details/id_' . $result_news[$j]['id']);
		$results_mods[$i]['results'][$j]['headline'] = $result_news[$j]['headline'];

		$striped_text = strip_tags($result_news[$j]['text']);
		$striped_text = $db->escape($striped_text, 3);
		$striped_text = html_entity_decode($striped_text, ENT_QUOTES, 'UTF-8');
		$striped_text = substr($striped_text, 0, 200);

		$results_mods[$i]['results'][$j]['text'] = htmlentities($striped_text, ENT_QUOTES, 'UTF-8') . '...';
	}
}
?>