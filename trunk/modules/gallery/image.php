<?php
/**
 * Gallery
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit;

if (ACP3_Validate::isNumber(ACP3_CMS::$uri->id) === true) {
	@set_time_limit(20);
	$picture = ACP3_CMS::$db2->fetchColumn('SELECT file FROM ' . DB_PRE . 'gallery_pictures WHERE id = ?', array(ACP3_CMS::$uri->id));
	$action = ACP3_CMS::$uri->action === 'thumb' ? 'thumb' : '';

	$settings = ACP3_Config::getSettings('gallery');
	$options = array(
		'enable_cache' => CONFIG_CACHE_IMAGES,
		'cache_prefix' => 'gallery_' . $action,
		'max_width' => $settings[$action . 'width'],
		'max_height' => $settings[$action . 'height'],
		'file' => UPLOADS_DIR . 'gallery/' . $picture,
		'prefer_height' => $action === 'thumb' ? true : false
	);

	$image = new ACP3_Image($options);
	$image->output();

	ACP3_CMS::$view->setNoOutput(true);
}