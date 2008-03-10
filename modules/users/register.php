<?php
if (!defined('IN_ACP3'))
	exit;

if ($auth->is_user()) {
	redirect(0, ROOT_DIR);
} else {
	$breadcrumb->assign(lang('users', 'users'), uri('users'));
	$breadcrumb->assign(lang('users', 'register'));

	if (isset($_POST['submit'])) {
		$form = $_POST['form'];

		if (empty($form['nickname']))
			$errors[] = lang('common', 'name_to_short');
		if (!empty($form['nickname']) && $db->select('id', 'users', 'nickname = \'' . $db->escape($form['nickname']) . '\'', 0, 0, 0, 1) == '1')
			$errors[] = lang('users', 'user_name_already_exists');
		if (!$validate->email($form['mail']))
			$errors[] = lang('common', 'wrong_email_format');
		if ($validate->email($form['mail']) && $db->select('id', 'users', 'mail =\'' . $form['mail'] . '\'', 0, 0, 0, 1) > 0)
			$errors[] = lang('common', 'user_email_already_exists');
		if (empty($form['pwd']) || empty($form['pwd_repeat']) || $form['pwd'] != $form['pwd_repeat'])
			$errors[] = lang('users', 'type_in_pwd');

		if (isset($errors)) {
			combo_box($errors);
		} else {
			$salt = salt(12);

			// E-Mail mit den Accountdaten zusenden
			$subject = sprintf(lang('users', 'register_mail_subject'), CONFIG_TITLE, htmlentities($_SERVER['HTTP_HOST']));
			$message = sprintf(lang('users', 'register_mail_message'), $db->escape($form['nickname']), CONFIG_TITLE, htmlentities($_SERVER['HTTP_HOST']), $form['mail'], $form['pwd']);
			$header = 'Content-type: text/plain; charset=UTF-8';
			$mail_sent = @mail($form['mail'], $subject, $message, $header);

			// Das Benutzerkonto nur erstellen, wenn die E-Mail erfolgreich versandt werden konnte
			if ($mail_sent) {
				$insert_values = array(
					'id' => '',
					'nickname' => $db->escape($form['nickname']),
					'realname' => '',
					'pwd' => sha1($salt . sha1($form['pwd'])) . ':' . $salt,
					'access' => '3',
					'mail' => $form['mail'],
					'website' => '',
					'time_zone' => CONFIG_TIME_ZONE,
					'dst' => CONFIG_DST,
					'language' => CONFIG_LANG,
					'draft' => '',
				);

				$bool = $db->insert('users', $insert_values);
			}

			$content = combo_box($mail_sent && isset($bool) && $bool ? lang('users', 'register_success') : lang('users', 'register_error'), ROOT_DIR);
		}
	}
	if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
		$tpl->assign('form', isset($form) ? $form : '');

		$content = $tpl->fetch('users/register.html');
	}
}
?>