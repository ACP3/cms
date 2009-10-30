<?php
/**
 * Feeds
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3'))
	exit;

$time = $date->timestamp();
$result = $db->select('id, start, headline, text', 'news', '(start = end AND start <= \'' . $time . '\' OR start != end AND start <= \'' . $time . '\' AND end >= \'' . $time . '\')', 'start DESC, end DESC, id DESC', 10);
$c_result = count($result);

if ($c_result > 0) {
	$results = NULL;
	for ($i = 0; $i < $c_result; ++$i) {
		$results[$i]['date'] = $date->format($result[$i]['start'], 'r');
		$results[$i]['title'] = html_entity_decode($result[$i]['headline'], ENT_QUOTES, 'UTF-8');
		$results[$i]['description'] = shortenEntry($db->escape($result[$i]['text'], 3), 300, 0);
		$results[$i]['uri'] = $link . uri('news/details/id_' . $result[$i]['id']);
	}
	$tpl->assign('results', $results);
}
