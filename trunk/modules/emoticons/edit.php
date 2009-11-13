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

if (validate::isNumber($uri->id) && $db->countRows('*', 'emoticons', 'id = \'' . $uri->id . '\'') == '1') {
	require_once ACP3_ROOT . 'modules/emoticons/functions.php';

	if (isset($_POST['submit'])) {
		$form = $_POST['form'];
		if (!empty($_FILES['picture']['tmp_name'])) {
			$file['tmp_name'] = $_FILES['picture']['tmp_name'];
			$file['name'] = $_FILES['picture']['name'];
			$file['size'] = $_FILES['picture']['size'];
		}
		$settings = config::output('emoticons');

		if (empty($form['code']))
			$errors[] = $lang->t('emoticons', 'type_in_code');
		if (empty($form['description']))
			$errors[] = $lang->t('emoticons', 'type_in_description');
		if (!empty($file['tmp_name']) && !validate::isPicture($file['tmp_name'], $settings['width'], $settings['height'], $settings['filesize']))
			$errors[] = $lang->t('emoticons', 'invalid_image_selected');

		if (isset($errors)) {
			$tpl->assign('error_msg', comboBox($errors));
		} else {
			$new_file_sql = null;
			if (isset($file)) {
				$result = moveFile($file['tmp_name'], $file['name'], 'emoticons');
				$new_file_sql['img'] = $result['name'];
			}

			$update_values = array(
				'code' => db::escape($form['code']),
				'description' => db::escape($form['description']),
			);
			if (is_array($new_file_sql)) {
				$old_file = $db->select('img', 'emoticons', 'id = \'' . $uri->id . '\'');
				removeFile('emoticons', $old_file[0]['img']);

				$update_values = array_merge($update_values, $new_file_sql);
			}

			$bool = $db->update('emoticons', $update_values, 'id = \'' . $uri->id . '\'');
			setEmoticonsCache();

			$content = comboBox($bool !== null ? $lang->t('common', 'edit_success') : $lang->t('common', 'edit_error'), uri('acp/emoticons'));
		}
	}
	if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
		$emoticon = $db->select('code, description, img', 'emoticons', 'id = \'' . $uri->id . '\'');

		$tpl->assign('picture', $emoticon[0]['img']);
		$tpl->assign('form', isset($form) ? $form : $emoticon[0]);

		$content = $tpl->fetch('emoticons/edit.html');
	}
} else {
	redirect('errors/404');
}
