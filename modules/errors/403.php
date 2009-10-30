<?php
/**
 * Errors
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3') && !defined('IN_ADM'))
	exit;

header('HTTP/1.0 403 Forbidden');
$content = $tpl->fetch('errors/403.html');
