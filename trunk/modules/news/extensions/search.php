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
		$results_mods['news']['results'][$i]['hyperlink'] = $uri->route('news/details/id_' . $result_news[$i]['id'], 1);
		$results_mods['news']['results'][$i]['headline'] = $result_news[$i]['headline'];
		$results_mods['news']['results'][$i]['text'] = shortenEntry($result_news[$i]['text'], 200, 0, '...');
	}
}