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

if (ACP3_Modules::check(ACP3_CMS::$uri->feed, 'extensions/feeds') === true) {
	$settings = ACP3_Config::getSettings('feeds');

	define('FEED_LINK', 'http://' . htmlentities($_SERVER['HTTP_HOST'], ENT_QUOTES));

	require LIBRARIES_DIR . 'feedcreator/FeedWriter.php';
	require LIBRARIES_DIR . 'feedcreator/FeedItem.php';

	$config = array(
		'feed_image' => $settings['feed_image'],
		'feed_type' => $settings['feed_type'],
		'feed_link' => FEED_LINK . ROOT_DIR,
		'feed_title' => CONFIG_SEO_TITLE,
		'module' => ACP3_CMS::$uri->feed,
	);

	ACP3_View::factory('FeedGenerator', $config);

	require MODULES_DIR . ACP3_CMS::$uri->feed . '/extensions/feeds.php';
	
	ACP3_CMS::$view->setLayout('');
	ACP3_CMS::$view->setContentType('text/xml');
	ACP3_CMS::$view->setContent(ACP3_CMS::$view->getRenderer()->display($settings['feed_type']));
}