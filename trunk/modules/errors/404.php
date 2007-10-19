<?php
/**
 * Errors
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_FRONTEND') && !defined('IN_ACP'))
	exit;

$content = $tpl->fetch('errors/404.html');
?>