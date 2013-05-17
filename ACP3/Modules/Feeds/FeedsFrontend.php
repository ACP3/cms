<?php

namespace ACP3\Modules\Feeds;

use ACP3\Core;

/**
 * Description of FeedsFrontend
 *
 * @author Tino
 */
class FeedsFrontend extends Core\ModuleController {

	public function actionList() {
		if (Core\Modules::check(Core\Registry::get('URI')->feed, 'extensions/feeds') === true) {
			$settings = Core\Config::getSettings('feeds');

			define('FEED_LINK', 'http://' . htmlentities($_SERVER['HTTP_HOST'], ENT_QUOTES));

			require LIBRARIES_DIR . 'feedcreator/FeedWriter.php';
			require LIBRARIES_DIR . 'feedcreator/FeedItem.php';

			$config = array(
				'feed_image' => $settings['feed_image'],
				'feed_type' => $settings['feed_type'],
				'feed_link' => FEED_LINK . ROOT_DIR,
				'feed_title' => CONFIG_SEO_TITLE,
				'module' => Core\Registry::get('URI')->feed,
			);

			Core\View::factory('FeedGenerator', $config);

			require MODULES_DIR . Core\Registry::get('URI')->feed . '/extensions/feeds.php';

			Core\Registry::get('View')->setNoOutput(true);
			Core\Registry::get('View')->setContentType('text/xml');
			Core\Registry::get('View')->display($settings['feed_type']);
		}
	}

}