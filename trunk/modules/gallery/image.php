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

if (validate::isNumber($uri->id)) {
	@set_time_limit(20);
	$picture = $db->select('file', 'gallery_pictures', 'id = \'' . $uri->id . '\'');

	$settings = config::getModuleSettings('gallery');
	$options = array(
		'cache_picture' => true,
		'cache_dir' => ACP3_ROOT . 'uploads/gallery/cache/',
		'cache_prefix' => $uri->action === 'thumb' ? 'thumb' : '',
		'max_width' => $uri->action === 'thumb' ? $settings['thumbwidth'] : $settings['width'],
		'max_height' => $uri->action === 'thumb' ? $settings['thumbheight'] : $settings['height'],
		'file' => ACP3_ROOT . 'uploads/gallery/' . $picture[0]['file'],
	);

	$image = new image($options);
	$image->output();

	exit;
}