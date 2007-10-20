<?php
/**
 * Users
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3'))
	exit;

setcookie('ACP3_AUTH', '', time() - 3600, '/');

$_SESSION = array();
if (isset($_COOKIE[session_name()]))
	setcookie(session_name(), '', time() - 3600, '/');
session_destroy();

redirect(0, ROOT_DIR);
?>