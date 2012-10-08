<?php
/**
 * News
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit;

switch($_POST['area']) {
	case 'title':
		$fields = 'title';
		break;
	case 'content':
		$fields = 'text';
		break;
	default:
		$fields = 'title, text';
}

$period = '(start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)';
$result_news = ACP3_CMS::$db2->fetchAll('SELECT id, title, text FROM ' . DB_PRE . 'news WHERE MATCH (' . $fields . ') AGAINST (' . ACP3_CMS::$db2->quote($_POST['search_term']) . ' IN BOOLEAN MODE) AND ' . $period . ' ORDER BY start ' . $_POST['sort'] . ', end ' . $_POST['sort'] . ', id ' . $_POST['sort'], array('time' => ACP3_CMS::$date->getCurrentDateTime()));
$c_result_news = count($result_news);

if ($c_result_news > 0) {
	$name =  ACP3_CMS::$lang->t('news', 'news');
	$results_mods[$name]['dir'] = 'news';
	for ($i = 0; $i < $c_result_news; ++$i) {
		$results_mods[$name]['results'][$i]['hyperlink'] = ACP3_CMS::$uri->route('news/details/id_' . $result_news[$i]['id']);
		$results_mods[$name]['results'][$i]['title'] = $result_news[$i]['title'];
		$results_mods[$name]['results'][$i]['text'] = shortenEntry($result_news[$i]['text'], 200, 0, '...');
	}
}