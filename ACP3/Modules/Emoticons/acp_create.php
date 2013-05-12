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
	if (!isset($file) ||
		ACP3\Core\Validate::isPicture($file['tmp_name'], $settings['width'], $settings['height'], $settings['filesize']) === false ||
		$_FILES['picture']['error'] !== UPLOAD_ERR_OK)
		$errors['picture'] = ACP3\CMS::$injector['Lang']->t('emoticons', 'invalid_image_selected');

	if (isset($errors) === true) {
		ACP3\CMS::$injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
	} elseif (ACP3\Core\Validate::formToken() === false) {
		ACP3\CMS::$injector['View']->setContent(Core\Functions::errorBox(ACP3\CMS::$injector['Lang']->t('system', 'form_already_submitted')));
	} else {
		$result = moveFile($file['tmp_name'], $file['name'], 'emoticons');

		$insert_values = array(
			'id' => '',
			'code' => ACP3\Core\Functions::str_encode($_POST['code']),
			'description' => ACP3\Core\Functions::str_encode($_POST['description']),
			'img' => $result['name'],
		);

		$bool = ACP3\CMS::$injector['Db']->insert(DB_PRE . 'emoticons', $insert_values);
		setEmoticonsCache();

		ACP3\CMS::$injector['Session']->unsetFormToken();

		ACP3\Core\Functions::setRedirectMessage($bool, ACP3\CMS::$injector['Lang']->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/emoticons');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	ACP3\CMS::$injector['View']->assign('form', isset($_POST['submit']) ? $_POST : array('code' => '', 'description' => ''));

	ACP3\CMS::$injector['Session']->generateFormToken();
}
