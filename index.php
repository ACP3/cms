<?php
/**
 * Index
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */

ob_start();

define('ACP3_ROOT', './');
require ACP3_ROOT . 'includes/common.php';

$tpl->assign('lang', CONFIG_LANG);
$tpl->assign('page_title', CONFIG_TITLE);
$tpl->assign('keywords', CONFIG_META_KEYWORDS);
$tpl->assign('description', CONFIG_META_DESCRIPTION);

if (CONFIG_MAINTENANCE == '1' && defined('IN_ACP3')) {
	$tpl->assign('maintenance_msg', CONFIG_MAINTENANCE_MSG);
	$tpl->display('offline.html');
} else {
	$auth = new auth;

	if ($modules->check()) {
		include ACP3_ROOT . 'modules/' . $modules->mod . '/' . $modules->page . '.php';
		$tpl->assign('content', isset($content) ? $content : '');
	} elseif (!$auth->isUser() && defined('IN_ADM') && $modules->mod != 'users' && $modules->page != 'login') {
		redirect('users/login');
	} elseif (is_file(ACP3_ROOT . 'modules/errors/404.php')) {
		redirect('errors/404');
	}

	// Evtl. gesetzten Content-Type des Servers überschreiben
	header('Content-Type: ' . (defined('CUSTOM_CONTENT_TYPE') ? CUSTOM_CONTENT_TYPE : 'text/html') . '; charset=UTF-8');

	// Navigationsleisten
	if ($modules->check('pages', 'functions')) {
		include_once ACP3_ROOT . 'modules/pages/functions.php';
		$tpl->assign('navbar', process_navbar());
	}

	// Template ausgeben
	$tpl->assign('title', $breadcrumb->output(2));
	$tpl->assign('breadcrumb', $breadcrumb->output());

	// Falls ein Modul ein eigenes Layout verwenden möchte, dieses auch verweden
	$tpl->display(defined('CUSTOM_LAYOUT') ? CUSTOM_LAYOUT : 'layout.html');
}

ob_end_flush();
?>