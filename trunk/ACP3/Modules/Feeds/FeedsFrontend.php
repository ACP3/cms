<?php

namespace ACP3\Modules\Feeds;

use ACP3\Core;

/**
 * Description of FeedsFrontend
 *
 * @author Tino
 */
class FeedsFrontend extends Core\ModuleController {

	public function __construct($injector) {
		parent::__construct($injector);
	}

	public function actionList() {
		if (Core\Modules::check($this->injector['URI']->feed, 'extensions/feeds') === true) {
			$settings = Core\Config::getSettings('feeds');

			define('FEED_LINK', 'http://' . htmlentities($_SERVER['HTTP_HOST'], ENT_QUOTES));

			require LIBRARIES_DIR . 'feedcreator/FeedWriter.php';
			require LIBRARIES_DIR . 'feedcreator/FeedItem.php';

			$config = array(
				'feed_image' => $settings['feed_image'],
				'feed_type' => $settings['feed_type'],
				'feed_link' => FEED_LINK . ROOT_DIR,
				'feed_title' => CONFIG_SEO_TITLE,
				'module' => $this->injector['URI']->feed,
			);

			Core\View::factory('FeedGenerator', $config);

			require MODULES_DIR . $this->injector['URI']->feed . '/extensions/feeds.php';

			$this->injector['View']->setNoOutput(true);
			$this->injector['View']->setContentType('text/xml');
			$this->injector['View']->display($settings['feed_type']);
		}
	}

}