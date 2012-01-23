<?php
/**
 * Errors
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit;

header('HTTP/1.0 404 not found');
view::setContent(view::fetchTemplate('errors/404.tpl'));
