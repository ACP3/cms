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

$time = ACP3_CMS::$date->getCurrentDateTime();
$result = ACP3_CMS::$db->select('id, start, headline, text', 'news', '(start = end AND start <= \'' . $time . '\' OR start != end AND start <= \'' . $time . '\' AND end >= \'' . $time . '\')', 'start DESC, end DESC, id DESC', 10);
$c_result = count($result);

for ($i = 0; $i < $c_result; ++$i) {
	$item = $feed->createNewItem();
	$item->setTitle(ACP3_CMS::$db->escape($result[$i]['headline'], 3));
	$item->setDate(ACP3_CMS::$date->timestamp($result[$i]['start']));
	$item->setDescription(shortenEntry(ACP3_CMS::$db->escape($result[$i]['text'], 3), 300, 0));
	$link = FEED_LINK . ACP3_CMS::$uri->route('news/details/id_' . $result[$i]['id'], false);
	$item->setLink($link);
	if ($settings['feed_type'] !== 'ATOM') {
		$item->addElement('guid', $link, array('isPermaLink' => 'true'));
	}
	$feed->addItem($item);
}