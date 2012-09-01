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

if (ACP3_Modules::check($uri->feed, 'extensions/feeds') === true) {
	$settings = ACP3_Config::getSettings('feeds');

	define('FEED_LINK', 'http://' . htmlentities($_SERVER['HTTP_HOST'], ENT_QUOTES));

	require LIBRARIES_DIR . 'feedcreator/FeedWriter.php';
	require LIBRARIES_DIR . 'feedcreator/FeedItem.php';

	$module = $uri->feed;
	
	$feed = new FeedWriter($settings['feed_type']);

	$feed->setTitle(CONFIG_SEO_TITLE);
	$feed->setLink(FEED_LINK . ROOT_DIR);
	if ($settings['feed_type'] !== 'ATOM') {
		$feed->setDescription($lang->t($module, $module));
	} else {
		$feed->setChannelElement('updated', date(DATE_ATOM , time()));
		$feed->setChannelElement('author', array('name' => CONFIG_SEO_TITLE));
	}

	$feed->setImage(CONFIG_SEO_TITLE, FEED_LINK . ROOT_DIR, $settings['feed_image']);

	require MODULES_DIR . $module . '/extensions/feeds.php';

	$feed->genarateFeed();

	ACP3_View::setNoOutput(true);
}