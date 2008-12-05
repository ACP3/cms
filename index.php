<?php
/**
 * Index
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */
define('ACP3_ROOT', './');
require ACP3_ROOT . 'includes/common.php';

if (modules::check()) {
	include ACP3_ROOT . 'modules/' . $uri->mod . '/' . $uri->page . '.php';

	// Evtl. gesetzten Content-Type des Servers überschreiben
	header('Content-Type: ' . (defined('CUSTOM_CONTENT_TYPE') ? CUSTOM_CONTENT_TYPE : 'text/html') . '; charset=UTF-8');

	$tpl->assign('TITLE', breadcrumb::output(2));
	$tpl->assign('BREADCRUMB', breadcrumb::output());
	$tpl->assign('CONTENT', !empty($content) ? $content : '');

	// Falls ein Modul ein eigenes Layout verwenden möchte, dieses auch verwenden
	$tpl->display(defined('CUSTOM_LAYOUT') ? CUSTOM_LAYOUT : 'layout.html');
} elseif (!$auth->isUser() && defined('IN_ADM') && $uri->mod != 'users' && $uri->page != 'login') {
	redirect('acp/users/login');
} else {
	redirect('errors/404');
}
?>