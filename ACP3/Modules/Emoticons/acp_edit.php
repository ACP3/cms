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

if (ACP3\Core\Validate::isNumber(ACP3\CMS::$injector['URI']->id) === true &&
	ACP3\CMS::$injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'emoticons WHERE id = ?', array(ACP3\CMS::$injector['URI']->id)) == 1) {
	require_once MODULES_DIR . 'emoticons/functions.php';

	if (isset($_POST['submit']) === true) {
		if (!empty($_FILES['picture']['tmp_name'])) {
			$file['tmp_name'] = $_FILES['picture']['tmp_name'];
			$file['name'] = $_FILES['picture']['name'];
			$file['size'] = $_FILES['picture']['size'];
		}
		$settings = ACP3\Core\Config::getSettings('emoticons');

		if (empty($_POST['code']))
			$errors['code'] = ACP3\CMS::$injector['Lang']->t('emoticons', 'type_in_code');
		if (empty($_POST['description']))
			$errors['description'] = ACP3\CMS::$injector['Lang']->t('emoticons', 'type_in_description');
		if (!empty($file['tmp_name']) &&
			(ACP3\Core\Validate::isPicture($file['tmp_name'], $settings['width'], $settings['height'], $settings['filesize']) === false ||
			$_FILES['picture']['error'] !== UPLOAD_ERR_OK))
			$errors['picture'] = ACP3\CMS::$injector['Lang']->t('emoticons', 'invalid_image_selected');

		if (isset($errors) === true) {
			ACP3\CMS::$injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
		} elseif (ACP3\Core\Validate::formToken() === false) {
			ACP3\CMS::$injector['View']->setContent(Core\Functions::errorBox(ACP3\CMS::$injector['Lang']->t('system', 'form_already_submitted')));
		} else {
			$new_file_sql = null;
			if (isset($file)) {
				$result = moveFile($file['tmp_name'], $file['name'], 'emoticons');
				$new_file_sql['img'] = $result['name'];
			}

			$update_values = array(
				'code' => ACP3\Core\Functions::str_encode($_POST['code']),
				'description' => ACP3\Core\Functions::str_encode($_POST['description']),
			);
			if (is_array($new_file_sql) === true) {
				$old_file = ACP3\CMS::$injector['Db']->fetchColumn('SELECT img FROM emoticons WHERE id = ?', array(ACP3\CMS::$injector['URI']->id));
				removeUploadedFile('emoticons', $old_file);

				$update_values = array_merge($update_values, $new_file_sql);
			}

			$bool = ACP3\CMS::$injector['Db']->update(DB_PRE . 'emoticons', $update_values, array('id' => ACP3\CMS::$injector['URI']->id));
			setEmoticonsCache();

			ACP3\CMS::$injector['Session']->unsetFormToken();

			ACP3\Core\Functions::setRedirectMessage($bool, ACP3\CMS::$injector['Lang']->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/emoticons');
		}
	}
	if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		$emoticon = ACP3\CMS::$injector['Db']->fetchAssoc('SELECT code, description FROM ' . DB_PRE . 'emoticons WHERE id = ?', array(ACP3\CMS::$injector['URI']->id));

		ACP3\CMS::$injector['View']->assign('form', isset($_POST['submit']) ? $_POST : $emoticon);

		ACP3\CMS::$injector['Session']->generateFormToken();
	}
} else {
	ACP3\CMS::$injector['URI']->redirect('errors/404');
}