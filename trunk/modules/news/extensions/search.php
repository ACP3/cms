<?php
/**
 * Search
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit;

switch($_POST['area']) {
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
$period = '(start = end AND start <= ' . $time . ' OR start != end AND start <= ' . $time . ' AND end >= ' . $time . ')';

$result_news = $db->select('id, headline, text', 'news', 'MATCH (' . $fields . ') AGAINST (\'' . $db->escape($_POST['search_term']) . '\' IN BOOLEAN MODE) AND ' . $period, 'start ' . $_POST['sort'] . ', end ' . $_POST['sort'] . ', id ' . $_POST['sort']);
$c_result_news = count($result_news);

if ($c_result_news > 0) {
	$results_mods['news']['title'] = $lang->t('news', 'news');
	for ($i = 0; $i < $c_result_news; ++$i) {
		$results_mods['news']['results'][$i]['hyperlink'] = $uri->route('news/details/id_' . $result_news[$i]['id'], 1);
		$results_mods['news']['results'][$i]['headline'] = $db->escape($result_news[$i]['headline'], 3);
		$results_mods['news']['results'][$i]['text'] = shortenEntry($db->escape($result_news[$i]['text'], 3), 200, 0, '...');
	}
}