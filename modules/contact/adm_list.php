<?php
/**
 * Contact
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

if (isset($_POST['submit'])) {
	include 'modules/contact/entry.php';
}
if (!isset($_POST['submit']) || isset($error_msg)) {
	$tpl->assign('error_msg', isset($error_msg) ? $error_msg : '');

	$contact = $config->output('contact');
	$contact['address'] = $contact['address'];
	$contact['disclaimer'] = $db->escape($contact['disclaimer'], 3);
	$contact['miscellaneous'] = $db->escape($contact['miscellaneous'], 3);

	$tpl->assign('form', isset($form) ? $form : $contact);

	$content = $tpl->fetch('contact/adm_list.html');
}
?>