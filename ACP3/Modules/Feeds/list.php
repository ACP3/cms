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

if (ACP3\Core\Modules::check(ACP3\CMS::$injector['URI']->feed, 'extensions/feeds') === true) {
	$settings = ACP3\Core\Config::getSettings('feeds');

	define('FEED_LINK', 'http://' . htmlentities($_SERVER['HTTP_HOST'], ENT_QUOTES));

	require LIBRARIES_DIR . 'feedcreator/FeedWriter.php';
	require LIBRARIES_DIR . 'feedcreator/FeedItem.php';

	$config = array(
		'feed_image' => $settings['feed_image'],
		'feed_type' => $settings['feed_type'],
		'feed_link' => FEED_LINK . ROOT_DIR,
		'feed_title' => CONFIG_SEO_TITLE,
		'module' => ACP3\CMS::$injector['URI']->feed,
	);

	ACP3\Core\View::factory('FeedGenerator', $config);

	require MODULES_DIR . ACP3\CMS::$injector['URI']->feed . '/extensions/feeds.php';
	
	ACP3\CMS::$injector['View']->setLayout('');
	ACP3\CMS::$injector['View']->setContentType('text/xml');
	ACP3\CMS::$injector['View']->setContent(ACP3\CMS::$injector['View']->getRenderer()->display($settings['feed_type']));
}