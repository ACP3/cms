<?php
/**
 * System
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('system/acp_maintenance.tpl'));
