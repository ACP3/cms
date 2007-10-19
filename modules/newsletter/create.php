<?php
/**
 * Newsletter
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_FRONTEND'))
	exit;

if (isset($_POST['submit'])) {
	include 'modules/newsletter/entry.php';
}
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
	$tpl->assign('form', isset($form) ? $form : '');

	$content = $tpl->fetch('newsletter/create.html');
}
?>