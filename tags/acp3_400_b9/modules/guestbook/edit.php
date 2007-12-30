<?php
/**
 * Guestbook
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

if (!empty($modules->id) && $db->select('id', 'guestbook', 'id = \'' . $modules->id . '\'', 0, 0, 0, 1) == '1') {
	if (isset($_POST['submit'])) {
		include 'modules/guestbook/entry.php';
	}
	if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
		$guestbook = $db->select('name, message', 'guestbook', 'id = \'' . $modules->id . '\'');

		$tpl->assign('form', isset($form) ? $form : $guestbook[0]);

		$content = $tpl->fetch('guestbook/edit.html');
	}
} else {
	redirect('errors/404');
}
?>