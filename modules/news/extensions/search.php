<?php
/**
 * Search
 *
 * @author Tino Goratsch
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
$time = ACP3_CMS::$date->getCurrentDateTime();
$period = '(start = end AND start <= \'' . $time . '\' OR start != end AND start <= \'' . $time . '\' AND end >= \'' . $time . '\')';

$result_news = ACP3_CMS::$db->select('id, headline, text', 'news', 'MATCH (' . $fields . ') AGAINST (\'' . ACP3_CMS::$db->escape($_POST['search_term']) . '\' IN BOOLEAN MODE) AND ' . $period, 'start ' . $_POST['sort'] . ', end ' . $_POST['sort'] . ', id ' . $_POST['sort']);
$c_result_news = count($result_news);

if ($c_result_news > 0) {
	$results_mods['news']['title'] = ACP3_CMS::$lang->t('news', 'news');
	for ($i = 0; $i < $c_result_news; ++$i) {
		$results_mods['news']['results'][$i]['hyperlink'] = ACP3_CMS::$uri->route('news/details/id_' . $result_news[$i]['id'], 1);
		$results_mods['news']['results'][$i]['headline'] = ACP3_CMS::$db->escape($result_news[$i]['headline'], 3);
		$results_mods['news']['results'][$i]['text'] = shortenEntry(ACP3_CMS::$db->escape($result_news[$i]['text'], 3), 200, 0, '...');
	}
}