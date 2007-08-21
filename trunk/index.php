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
	// Session starten
	session_start();

	// Loginfeld bzw. im eingeloggten Zustand, Anzeige der Module
	if (isset($_COOKIE['ACP3_AUTH'])) {
		$cookie = $db->escape($_COOKIE['ACP3_AUTH']);
		$cookie_arr = explode('|', $cookie);

		$user_check = $db->select('id, pwd, access', 'users', 'name=\'' . $cookie_arr[0] . '\'');
		if (count($user_check) == '1') {
			$db_password = substr($user_check[0]['pwd'], 0, 40);
			if ($db_password == $cookie_arr[1]) {
				define('IS_USER', true);

				// Falls nötig, Session neu setzen
				if (empty($_SESSION['acp3_id']) || empty($_SESSION['acp3_access'])) {
					$_SESSION['acp3_id'] = $user_check[0]['id'];
					$_SESSION['acp3_access'] = $user_check[0]['access'];
				}
			}
		}
		if (!defined('IS_USER')) {
			include 'modules/users/signoff.php';
		}
	} else {
		if (defined('IN_ADM') && $modules->mod != 'users' && $modules->page != 'login')
			redirect('acp/users/login');

		// Session für Gast User setzen
		$_SESSION['acp3_access'] = '2';
	}
	include 'modules/users/sidebar.php';

	// Navigationsleisten
	if ($modules->check('pages', 'functions')) {
		include_once 'modules/pages/functions.php';
		$tpl->assign('navbar', process_navbar());
	}

	if ($modules->check() && $modules->page != 'info') {
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