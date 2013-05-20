<?php

namespace ACP3\Modules\Feeds;

use ACP3\Core;

/**
 * Description of FeedsExtensions
 *
 * @author goratsch
 */
class FeedsExtensions {

	public function newsFeed() {
		$result = Core\Registry::get('Db')->fetchAll('SELECT id, start, title, text FROM ' . DB_PRE . 'news WHERE (start = end AND start <= :time OR start != end AND :time BETWEEN start AND end) ORDER BY start DESC, end DESC, id DESC LIMIT 10', array('time' => Core\Registry::get('Date')->getCurrentDateTime()));
		$c_result = count($result);

		for ($i = 0; $i < $c_result; ++$i) {
			$params = array(
				'title' => $result[$i]['title'],
				'date' => Core\Registry::get('Date')->timestamp($result[$i]['start']),
				'description' => Core\Functions::shortenEntry($result[$i]['text'], 300, 0),
				'link' => FEED_LINK . Core\Registry::get('URI')->route('news/details/id_' . $result[$i]['id'], false)
			);
			Core\Registry::get('View')->assign($params);
		}
	}

	public function filesFeed() {
		$result = Core\Registry::get('Db')->fetchAll('SELECT id, start, title, text FROM ' . DB_PRE . 'files WHERE (start = end AND start <= :time OR start != end AND :time BETWEEN start AND end) ORDER BY start DESC, end DESC, id DESC LIMIT 10', array('time' => Core\Registry::get('Date')->getCurrentDateTime()));
		$c_result = count($result);

		for ($i = 0; $i < $c_result; ++$i) {
			$params = array(
				'title' => $result[$i]['title'],
				'date' => Core\Registry::get('Date')->timestamp($result[$i]['start']),
				'description' => Core\Functions::shortenEntry($result[$i]['text'], 300, 0),
				'link' => FEED_LINK . Core\Registry::get('URI')->route('files/details/id_' . $result[$i]['id'], false)
			);
			Core\Registry::get('View')->assign($params);
		}
	}

}