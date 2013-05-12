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

if (ACP3\Core\Validate::isNumber(ACP3\CMS::$injector['URI']->id) === true) {
	@set_time_limit(20);
	$picture = ACP3\CMS::$injector['Db']->fetchColumn('SELECT file FROM ' . DB_PRE . 'gallery_pictures WHERE id = ?', array(ACP3\CMS::$injector['URI']->id));
	$action = ACP3\CMS::$injector['URI']->action === 'thumb' ? 'thumb' : '';

	$settings = ACP3\Core\Config::getSettings('gallery');
	$options = array(
		'enable_cache' => CONFIG_CACHE_IMAGES == 1 ? true : false,
		'cache_prefix' => 'gallery_' . $action,
		'max_width' => $settings[$action . 'width'],
		'max_height' => $settings[$action . 'height'],
		'file' => UPLOADS_DIR . 'gallery/' . $picture,
		'prefer_height' => $action === 'thumb' ? true : false
	);

	$image = new ACP3_Image($options);
	$image->output();

	ACP3\CMS::$injector['View']->setNoOutput(true);
}