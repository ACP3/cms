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

$result = $db->select('id, start, headline, text', 'news', '(start = end AND start <= \'' . date_aligned(2, time()) . '\' OR start != end AND start <= \'' . date_aligned(2, time()) . '\' AND end >= \'' . date_aligned(2, time()) . '\')', 'start DESC, id DESC', 10);
$c_result = count($result);

if ($c_result > 0) {
	$results = NULL;
	for ($i = 0; $i < $c_result; $i++) {
		$results[$i]['date'] = date_aligned(1, $result[$i]['start'], 'r');
		$results[$i]['title'] = html_entity_decode($result[$i]['headline']);

		$description = strip_tags($db->escape($result[$i]['text'], 3));
		$description = html_entity_decode($description, ENT_QUOTES, CHARSET);

		$results[$i]['description'] = substr($description, 0, 300);
		$results[$i]['uri'] = 'index.php?stm=news/details/id_' . $result[$i]['id'] . '/';
	}
	$tpl->assign('results', $results);
}
?>