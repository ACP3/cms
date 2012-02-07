<?php
/**
 * Gallery
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit;

if (validate::isNumber($uri->id) === true) {
	@set_time_limit(20);
	$picture = $db->select('file', 'gallery_pictures', 'id = \'' . $uri->id . '\'');
	$action = $uri->action === 'thumb' ? 'thumb' : '';

	$settings = config::getModuleSettings('gallery');
	$options = array(
		'enable_cache' => CONFIG_CACHE_IMAGES,
		'cache_prefix' => 'gallery_' . $action,
		'max_width' => $settings[$action . 'width'],
		'max_height' => $settings[$action . 'height'],
		'file' => ACP3_ROOT . 'uploads/gallery/' . $picture[0]['file'],
	);

	$image = new image($options);
	$image->output();

	exit;
}