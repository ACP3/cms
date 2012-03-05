<?php
/**
 * System
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

ACP3_View::setContent(ACP3_View::fetchTemplate('system/adm_list.tpl'));
