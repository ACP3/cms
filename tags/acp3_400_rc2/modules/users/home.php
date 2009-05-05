<?php
if (!defined('IN_ACP3'))
	exit;

if (!$auth->isUser() || !validate::isNumber(USER_ID)) {
	redirect('errors/403');
} else {
	breadcrumb::assign($lang->t('users', 'users'), uri('users'));
	breadcrumb::assign($lang->t('users', 'home'));

	if (isset($_POST['submit'])) {
		$form = $_POST['form'];

		$bool = $db->update('users', array('draft' => $db->escape($form['draft'], 2)), 'id = \'' . USER_ID . '\'');

		$content = comboBox($bool ? $lang->t('common', 'edit_success') : $lang->t('common', 'edit_error'), uri('users/home'));
	}
	if (!isset($_POST['submit'])) {
		$user = $db->select('draft', 'users', 'id = \'' . USER_ID . '\'');

		$tpl->assign('draft', $db->escape($user[0]['draft'], 3));

		$content = $tpl->fetch('users/home.html');
	}
}
?>