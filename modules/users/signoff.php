<?php
/**
 * Users
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3') && !defined('IN_ADM'))
	exit;

setcookie('ACP3_AUTH', '', time() - 3600, '/');

redirect(0, ROOT_DIR);
?>