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
		$fields = 'headline';
		break;
	case 'content':
		$fields = 'text';
		break;
	default:
		$fields = 'headline, text';
}
$time = $date->timestamp();
$period = '(start = end AND start <= \'' . $time . '\' OR start != end AND start <= \'' . $time . '\' AND end >= \'' . $time . '\')';

$result_news = $db->select('id, headline, text', 'news', 'MATCH (' . $fields . ') AGAINST (\'' . $db->escape($form['search_term']) . '\' IN BOOLEAN MODE) AND ' . $period, 'start ' . $form['sort'] . ', end ' . $form['sort'] . ', id ' . $form['sort']);
$c_result_news = count($result_news);

if ($c_result_news > 0) {
	$results_mods['news']['title'] = $lang->t('news', 'news');
	for ($i = 0; $i < $c_result_news; ++$i) {
		$results_mods['news']['results'][$i]['hyperlink'] = uri('news/details/id_' . $result_news[$i]['id']);
		$results_mods['news']['results'][$i]['headline'] = $result_news[$i]['headline'];

		$striped_text = strip_tags($result_news[$i]['text']);
		$striped_text = $db->escape($striped_text, 3);
		$striped_text = html_entity_decode($striped_text, ENT_QUOTES, 'UTF-8');
		$striped_text = substr($striped_text, 0, 200);

		$results_mods['news']['results'][$i]['text'] = htmlentities($striped_text, ENT_QUOTES, 'UTF-8') . '...';
	}
}
?>