<?php
/**
 * Newsletter
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3'))
	exit;

if (isset($_POST['submit'])) {
	include 'modules/newsletter/entry.php';
}
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
	$settings = $config->output('newsletter');

	$tpl->assign('form', isset($form) ? $form : $settings);

	$content = $tpl->fetch('newsletter/acp_settings.html');
}
?>