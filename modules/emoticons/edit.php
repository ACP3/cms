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

if (!empty($modules->id) && $db->select('id', 'emoticons', 'id = \'' . $modules->id . '\'', 0, 0, 0, 1) == '1') {
	if (isset($_POST['submit'])) {
		$form = $_POST['form'];
		if (!empty($_FILES['picture']['tmp_name'])) {
			$file['tmp_name'] = $_FILES['picture']['tmp_name'];
			$file['name'] = $_FILES['picture']['name'];
			$file['size'] = $_FILES['picture']['size'];
		}

		if (empty($form['code']))
			$errors[] = lang('emoticons', 'type_in_code');
		if (empty($form['description']))
			$errors[] = lang('emoticons', 'type_in_description');
		if (isset($file) && (empty($file['size']) || !$validate->is_picture($file['tmp_name'])))
			$errors[] = lang('emoticons', 'select_picture');

		if (isset($errors)) {
			combo_box($errors);
		} else {
			$new_file_sql = null;
			if (isset($file)) {
				$result = move_file($file['tmp_name'], $file['name'], 'emoticons');
				$new_file_sql = array('img' => $result['name'],);
			}

			$update_values = array(
				'code' => $db->escape($form['code']),
				'description' => $db->escape($form['description']),
			);
			if (is_array($new_file_sql)) {
				$update_values = array_merge($update_values, $new_file_sql);
			}

			$bool = $db->update('emoticons', $update_values, 'id = \'' . $modules->id . '\'');

			$cache->create('emoticons', $db->select('code, description, img', 'emoticons'));

			$content = combo_box($bool ? lang('emoticons', 'edit_success') : lang('emoticons', 'edit_error'), uri('acp/emoticons'));
		}
	}
	if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
		$emoticon = $db->select('code, description, img', 'emoticons', 'id = \'' . $modules->id . '\'');

		$tpl->assign('picture', $emoticon[0]['img']);
		$tpl->assign('form', isset($form) ? $form : $emoticon[0]);

		$content = $tpl->fetch('emoticons/edit.html');
	}
} else {
	redirect('errors/404');
}
?>