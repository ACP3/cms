<?php
/**
 * Errors
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit;

header('HTTP/1.0 404 not found');
ACP3_View::setContent(ACP3_View::fetchTemplate('errors/404.tpl'));
