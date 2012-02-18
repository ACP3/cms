<?php
/**
 * Emoticons
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (validate::isNumber($uri->id) === true && $db->countRows('*', 'emoticons', 'id = \'' . $uri->id . '\'') == 1) {
	require_once MODULES_DIR . 'emoticons/functions.php';

	if (isset($_POST['form']) === true) {
		$form = $_POST['form'];
		if (!empty($_FILES['picture']['tmp_name'])) {
			$file['tmp_name'] = $_FILES['picture']['tmp_name'];
			$file['name'] = $_FILES['picture']['name'];
			$file['size'] = $_FILES['picture']['size'];
		}
		$settings = config::getModuleSettings('emoticons');

		if (empty($form['code']))
			$errors['code'] = $lang->t('emoticons', 'type_in_code');
		if (empty($form['description']))
			$errors['description'] = $lang->t('emoticons', 'type_in_description');
		if (!empty($file['tmp_name']) &&
			(validate::isPicture($file['tmp_name'], $settings['width'], $settings['height'], $settings['filesize']) === false ||
			$_FILES['picture']['error'] !== UPLOAD_ERR_OK))
			$errors['picture'] = $lang->t('emoticons', 'invalid_image_selected');

		if (isset($errors) === true) {
			$tpl->assign('error_msg', errorBox($errors));
		} elseif (validate::formToken() === false) {
			view::setContent(errorBox($lang->t('common', 'form_already_submitted')));
		} else {
			$new_file_sql = null;
			if (isset($file)) {
				$result = moveFile($file['tmp_name'], $file['name'], 'emoticons');
				$new_file_sql['img'] = $result['name'];
			}

			$update_values = array(
				'code' => $db->escape($form['code']),
				'description' => $db->escape($form['description']),
			);
			if (is_array($new_file_sql) === true) {
				$old_file = $db->select('img', 'emoticons', 'id = \'' . $uri->id . '\'');
				removeUploadedFile('emoticons', $old_file[0]['img']);

				$update_values = array_merge($update_values, $new_file_sql);
			}

			$bool = $db->update('emoticons', $update_values, 'id = \'' . $uri->id . '\'');
			setEmoticonsCache();

			$session->unsetFormToken();

			setRedirectMessage($bool !== false ? $lang->t('common', 'edit_success') : $lang->t('common', 'edit_error'), 'acp/emoticons');
		}
	}
	if (isset($_POST['form']) === false || isset($errors) === true && is_array($errors) === true) {
		$emoticon = $db->select('code, description, img', 'emoticons', 'id = \'' . $uri->id . '\'');
		$emoticon[0]['code'] = $db->escape($emoticon[0]['code'], 3);
		$emoticon[0]['description'] = $db->escape($emoticon[0]['description'], 3);

		$tpl->assign('picture', $emoticon[0]['img']);
		$tpl->assign('form', isset($form) ? $form : $emoticon[0]);

		$session->generateFormToken();

		view::setContent(view::fetchTemplate('emoticons/edit.tpl'));
	}
} else {
	$uri->redirect('errors/404');
}
