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

if (defined('IN_ADM'))
	$tpl->assign('uri', uri('acp/users/login'));
elseif (defined('IN_ACP3'))
	$tpl->assign('uri', uri('users/login'));

$field = $tpl->fetch('users/sidebar.html');
?>