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

header('HTTP/1.0 403 Forbidden');
ACP3_View::setContent(ACP3_View::fetchTemplate('errors/403.tpl'));
