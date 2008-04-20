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

$result = $db->select('id, start, link_title, text', 'files', '(start = end AND start <= \'' . dateAligned(2, time()) . '\' OR start != end AND start <= \'' . dateAligned(2, time()) . '\' AND end >= \'' . dateAligned(2, time()) . '\')', 'start DESC, id DESC', 10);
$c_result = count($result);

if ($c_result > 0) {
	$results = NULL;
	for ($i = 0; $i < $c_result; ++$i) {
		$results[$i]['date'] = dateAligned(1, $result[$i]['start'], 'r');
		$results[$i]['title'] = html_entity_decode($result[$i]['link_title']);

		$description = strip_tags($db->escape($result[$i]['text'], 3));
		$description = html_entity_decode($description, ENT_QUOTES);

		$results[$i]['description'] = substr($description, 0, 300);
		$results[$i]['uri'] = $link . uri('files/details/id_' . $result[$i]['id']);
	}
	$tpl->assign('results', $results);
}
?>