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

if (ACP3_Validate::isNumber(ACP3_CMS::$uri->id) === true && ACP3_CMS::$db->countRows('*', 'emoticons', 'id = \'' . ACP3_CMS::$uri->id . '\'') == 1) {
	require_once MODULES_DIR . 'emoticons/functions.php';

	if (isset($_POST['submit']) === true) {
		if (!empty($_FILES['picture']['tmp_name'])) {
			$file['tmp_name'] = $_FILES['picture']['tmp_name'];
			$file['name'] = $_FILES['picture']['name'];
			$file['size'] = $_FILES['picture']['size'];
		}
		$settings = ACP3_Config::getSettings('emoticons');

		if (empty($_POST['code']))
			$errors['code'] = ACP3_CMS::$lang->t('emoticons', 'type_in_code');
		if (empty($_POST['description']))
			$errors['description'] = ACP3_CMS::$lang->t('emoticons', 'type_in_description');
		if (!empty($file['tmp_name']) &&
			(ACP3_Validate::isPicture($file['tmp_name'], $settings['width'], $settings['height'], $settings['filesize']) === false ||
			$_FILES['picture']['error'] !== UPLOAD_ERR_OK))
			$errors['picture'] = ACP3_CMS::$lang->t('emoticons', 'invalid_image_selected');

		if (isset($errors) === true) {
			ACP3_CMS::$view->assign('error_msg', errorBox($errors));
		} elseif (ACP3_Validate::formToken() === false) {
			ACP3_CMS::setContent(errorBox(ACP3_CMS::$lang->t('common', 'form_already_submitted')));
		} else {
			$new_file_sql = null;
			if (isset($file)) {
				$result = moveFile($file['tmp_name'], $file['name'], 'emoticons');
				$new_file_sql['img'] = $result['name'];
			}

			$update_values = array(
				'code' => ACP3_CMS::$db->escape($_POST['code']),
				'description' => ACP3_CMS::$db->escape($_POST['description']),
			);
			if (is_array($new_file_sql) === true) {
				$old_file = ACP3_CMS::$db->select('img', 'emoticons', 'id = \'' . ACP3_CMS::$uri->id . '\'');
				removeUploadedFile('emoticons', $old_file[0]['img']);

				$update_values = array_merge($update_values, $new_file_sql);
			}

			$bool = ACP3_CMS::$db->update('emoticons', $update_values, 'id = \'' . ACP3_CMS::$uri->id . '\'');
			setEmoticonsCache();

			ACP3_CMS::$session->unsetFormToken();

			setRedirectMessage($bool, ACP3_CMS::$lang->t('common', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/emoticons');
		}
	}
	if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		$emoticon = ACP3_CMS::$db->select('code, description', 'emoticons', 'id = \'' . ACP3_CMS::$uri->id . '\'');
		$emoticon[0]['code'] = ACP3_CMS::$db->escape($emoticon[0]['code'], 3);
		$emoticon[0]['description'] = ACP3_CMS::$db->escape($emoticon[0]['description'], 3);

		ACP3_CMS::$view->assign('form', isset($_POST['submit']) ? $_POST : $emoticon[0]);

		ACP3_CMS::$session->generateFormToken();

		ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('emoticons/acp_edit.tpl'));
	}
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}