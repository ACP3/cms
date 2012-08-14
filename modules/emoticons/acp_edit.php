<?php
/**
 * Emoticons
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (ACP3_Validate::isNumber($uri->id) === true && $db->countRows('*', 'emoticons', 'id = \'' . $uri->id . '\'') == 1) {
	require_once MODULES_DIR . 'emoticons/functions.php';

	if (isset($_POST['submit']) === true) {
		if (!empty($_FILES['picture']['tmp_name'])) {
			$file['tmp_name'] = $_FILES['picture']['tmp_name'];
			$file['name'] = $_FILES['picture']['name'];
			$file['size'] = $_FILES['picture']['size'];
		}
		$settings = ACP3_Config::getModuleSettings('emoticons');

		if (empty($_POST['code']))
			$errors['code'] = $lang->t('emoticons', 'type_in_code');
		if (empty($_POST['description']))
			$errors['description'] = $lang->t('emoticons', 'type_in_description');
		if (!empty($file['tmp_name']) &&
			(ACP3_Validate::isPicture($file['tmp_name'], $settings['width'], $settings['height'], $settings['filesize']) === false ||
			$_FILES['picture']['error'] !== UPLOAD_ERR_OK))
			$errors['picture'] = $lang->t('emoticons', 'invalid_image_selected');

		if (isset($errors) === true) {
			$tpl->assign('error_msg', errorBox($errors));
		} elseif (ACP3_Validate::formToken() === false) {
			ACP3_View::setContent(errorBox($lang->t('common', 'form_already_submitted')));
		} else {
			$new_file_sql = null;
			if (isset($file)) {
				$result = moveFile($file['tmp_name'], $file['name'], 'emoticons');
				$new_file_sql['img'] = $result['name'];
			}

			$update_values = array(
				'code' => $db->escape($_POST['code']),
				'description' => $db->escape($_POST['description']),
			);
			if (is_array($new_file_sql) === true) {
				$old_file = $db->select('img', 'emoticons', 'id = \'' . $uri->id . '\'');
				removeUploadedFile('emoticons', $old_file[0]['img']);

				$update_values = array_merge($update_values, $new_file_sql);
			}

			$bool = $db->update('emoticons', $update_values, 'id = \'' . $uri->id . '\'');
			setEmoticonsCache();

			$session->unsetFormToken();

			setRedirectMessage($bool, $lang->t('common', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/emoticons');
		}
	}
	if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		$emoticon = $db->select('code, description', 'emoticons', 'id = \'' . $uri->id . '\'');
		$emoticon[0]['code'] = $db->escape($emoticon[0]['code'], 3);
		$emoticon[0]['description'] = $db->escape($emoticon[0]['description'], 3);

		$tpl->assign('form', isset($_POST['submit']) ? $_POST : $emoticon[0]);

		$session->generateFormToken();

		ACP3_View::setContent(ACP3_View::fetchTemplate('emoticons/acp_edit.tpl'));
	}
} else {
	$uri->redirect('errors/404');
}