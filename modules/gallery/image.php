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

if (ACP3_Validate::isNumber($uri->id) === true) {
	@set_time_limit(20);
	$picture = $db->select('file', 'gallery_pictures', 'id = \'' . $uri->id . '\'');
	$action = $uri->action === 'thumb' ? 'thumb' : '';

	$settings = ACP3_Config::getModuleSettings('gallery');
	$options = array(
		'enable_cache' => CONFIG_CACHE_IMAGES,
		'cache_prefix' => 'gallery_' . $action,
		'max_width' => $settings[$action . 'width'],
		'max_height' => $settings[$action . 'height'],
		'file' => ACP3_ROOT . 'uploads/gallery/' . $picture[0]['file'],
		'prefer_height' => $action === 'thumb' ? true : false
	);

	$image = new ACP3_Image($options);
	$image->output();

	exit;
}