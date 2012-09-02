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

header('HTTP/1.0 403 Forbidden');
ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('errors/403.tpl'));
