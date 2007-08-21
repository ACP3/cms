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

setcookie('ACP3_AUTH', '', time() - 3600, ROOT_DIR);

$_SESSION = array();

if (isset($_COOKIE[session_name()]))
	setcookie(session_name(), '', time() - 3600, ROOT_DIR);

session_destroy();

redirect('news/list');
?>