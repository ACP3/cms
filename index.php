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

if ($modules->check()) {
	include ACP3_ROOT . 'modules/' . $modules->mod . '/' . $modules->page . '.php';
	$tpl->assign('CONTENT', !empty($content) ? $content : '');
} elseif (!$auth->isUser() && defined('IN_ADM') && $modules->mod != 'users' && $modules->page != 'login') {
	redirect('users/login');
} else {
	redirect('errors/404');
}

// Evtl. gesetzten Content-Type des Servers überschreiben
header('Content-Type: ' . (defined('CUSTOM_CONTENT_TYPE') ? CUSTOM_CONTENT_TYPE : 'text/html') . '; charset=UTF-8');

// Seitentitel und Brotkrümelspur setzen
$tpl->assign('TITLE', breadcrumb::output(2));
$tpl->assign('BREADCRUMB', breadcrumb::output());

// Falls ein Modul ein eigenes Layout verwenden möchte, dieses auch verweden
$tpl->display(defined('CUSTOM_LAYOUT') ? CUSTOM_LAYOUT : 'layout.html');

ob_end_flush();
?>