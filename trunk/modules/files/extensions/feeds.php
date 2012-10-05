<?php
/**
 * Files
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit;

$result = ACP3_CMS::$db2->fetchAll('SELECT id, start, title, text FROM ' . DB_PRE . 'files WHERE (start = end AND start <= :time OR start != end AND :time BETWEEN start AND end) ORDER BY start DESC, end DESC, id DESC LIMIT 10', array('time' => ACP3_CMS::$date->getCurrentDateTime()));
$c_result = count($result);

for ($i = 0; $i < $c_result; ++$i) {
	$params = array(
		'title' => $result[$i]['title'],
		'date' => ACP3_CMS::$date->timestamp($result[$i]['start']),
		'description' => shortenEntry($result[$i]['text'], 300, 0),
		'link' => FEED_LINK . ACP3_CMS::$uri->route('files/details/id_' . $result[$i]['id'], false)
	);
	ACP3_CMS::$view->assign($params);
}
