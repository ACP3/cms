<?php
/**
 * Emoticons
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3'))
	exit;

if (!empty($modules->id) && $db->select('id', 'emoticons', 'id = \'' . $modules->id . '\'', 0, 0, 0, 1) == '1') {
	if (isset($_POST['submit'])) {
		include 'modules/emoticons/entry.php';
	}
	if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
		$emoticon = $db->select('code, description, img', 'emoticons', 'id = \'' . $modules->id . '\'');

		$tpl->assign('picture', $emoticon[0]['img']);
		$tpl->assign('form', isset($form) ? $form : $emoticon[0]);

		$content = $tpl->fetch('emoticons/acp_edit.html');
	}
} else {
	redirect('errors/404');
}
?>