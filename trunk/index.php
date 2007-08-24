<?php
/**
 * Index
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */

ob_start();

require 'includes/common.php';

$tpl->assign('lang', CONFIG_LANG);
$tpl->assign('page_title', CONFIG_TITLE);
$tpl->assign('keywords', CONFIG_META_KEYWORDS);
$tpl->assign('description', CONFIG_META_DESCRIPTION);

if (CONFIG_MAINTENANCE == '1' && defined('IN_ACP3')) {
	$tpl->assign('maintenance_msg', CONFIG_MAINTENANCE_MSG);
	$tpl->display('offline.html');
} else {
	$auth = new auth;

	if ($auth->is_guest() && defined('IN_ADM') && $modules->mod != 'users' && $modules->page != 'login') {
		redirect('acp/users/login');
	}

	include 'modules/users/sidebar.php';

	// Navigationsleisten
	if ($modules->check('pages', 'functions')) {
		include_once 'modules/pages/functions.php';
		$tpl->assign('navbar', process_navbar());
	}

	if ($modules->check()) {
		$content = '';
		include 'modules/' . $modules->mod . '/' . $modules->page . '.php';
		$tpl->assign('content', $content);
	} elseif (is_file('modules/errors/404.php')) {
		redirect('errors/404');
	}

	// Evtl. gesetzten Content-Type des Servers überschreiben
	header('Content-Type: ' . (defined('CUSTOM_CONTENT_TYPE') ? CUSTOM_CONTENT_TYPE : 'text/html') . '; charset=' . CHARSET);

	// Template ausgeben
	$tpl->assign('title', $breadcrumb->output(2));
	$tpl->assign('breadcrumb', $breadcrumb->output());

	// Falls ein Modul ein eigenes Layout verwenden möchte, dieses auch verweden
	$tpl->display(defined('CUSTOM_LAYOUT') ? CUSTOM_LAYOUT : 'layout.html');
}

ob_end_flush();
?>