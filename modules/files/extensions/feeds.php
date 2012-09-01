<?php
/**
 * Feeds
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit;

$time = $date->getCurrentDateTime();

$result = $db->select('id, start, link_title, text', 'files', '(start = end AND start <= \'' . $time . '\' OR start != end AND start <= \'' . $time . '\' AND end >= \'' . $time . '\')', 'start DESC, end DESC, id DESC', 10);
$c_result = count($result);

for ($i = 0; $i < $c_result; ++$i) {
	$item = $feed->createNewItem();
	$item->setTitle($db->escape($result[$i]['link_title'], 3));
	$item->setDate($date->timestamp($result[$i]['start']));
	$item->setDescription(shortenEntry($db->escape($result[$i]['text'], 3), 300, 0));
	$link = FEED_LINK . $uri->route('files/details/id_' . $result[$i]['id'], false);
	$item->setLink($link);
	if ($settings['feed_type'] !== 'ATOM') {
		$item->addElement('guid', $link, array('isPermaLink' => 'true'));
	}
	$feed->addItem($item);
}
