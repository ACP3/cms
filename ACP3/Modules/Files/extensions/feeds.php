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

$result = ACP3\CMS::$injector['Db']->fetchAll('SELECT id, start, title, text FROM ' . DB_PRE . 'files WHERE (start = end AND start <= :time OR start != end AND :time BETWEEN start AND end) ORDER BY start DESC, end DESC, id DESC LIMIT 10', array('time' => ACP3\CMS::$injector['Date']->getCurrentDateTime()));
$c_result = count($result);

for ($i = 0; $i < $c_result; ++$i) {
	$params = array(
		'title' => $result[$i]['title'],
		'date' => ACP3\CMS::$injector['Date']->timestamp($result[$i]['start']),
		'description' => ACP3\Core\Functions::shortenEntry($result[$i]['text'], 300, 0),
		'link' => FEED_LINK . ACP3\CMS::$injector['URI']->route('files/details/id_' . $result[$i]['id'], false)
	);
	ACP3\CMS::$injector['View']->assign($params);
}
