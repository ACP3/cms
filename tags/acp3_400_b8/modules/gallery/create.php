<?php
/**
 * Gallery
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

if (isset($_POST['submit'])) {
	include 'modules/gallery/entry.php';
}
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
	// Datumsauswahl
	$tpl->assign('start_date', publication_period('start'));
	$tpl->assign('end_date', publication_period('end'));

	$tpl->assign('form', isset($form) ? $form : '');

	$content = $tpl->fetch('gallery/create.html');
}
?>