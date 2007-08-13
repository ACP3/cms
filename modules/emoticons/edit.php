<?php
/**
 * Emoticons
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;
if (!$modules->check())
	redirect('errors/403');
if (!empty($modules->id) && $db->select('id', 'emoticons', 'id = \'' . $modules->id . '\'', 0, 0, 0, 1) == '1') {
	if (isset($_POST['submit'])) {
		include 'modules/emoticons/entry.php';
	}
	if (!isset($_POST['submit']) || isset($error_msg)) {
		$tpl->assign('error_msg', isset($error_msg) ? $error_msg : '');

		$emoticon = $db->select('code, description, img', 'emoticons', 'id = \'' . $modules->id . '\'');

		$tpl->assign('picture', $emoticon[0]['img']);
		$tpl->assign('form', isset($form) ? $form : $emoticon[0]);

		$content = $tpl->fetch('emoticons/edit.html');
	}
} else {
	redirect('errors/404');
}
?>