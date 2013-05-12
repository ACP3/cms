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

if (isset($_POST['submit']) === true) {
	if (ACP3\Core\Validate::isNumber($_POST['width']) === false)
		$errors['width'] = ACP3\CMS::$injector['Lang']->t('emoticons', 'invalid_image_width_entered');
	if (ACP3\Core\Validate::isNumber($_POST['height']) === false)
		$errors['height'] = ACP3\CMS::$injector['Lang']->t('emoticons', 'invalid_image_height_entered');
	if (ACP3\Core\Validate::isNumber($_POST['filesize']) === false)
		$errors['filesize'] = ACP3\CMS::$injector['Lang']->t('emoticons', 'invalid_image_filesize_entered');

	if (isset($errors) === true) {
		ACP3\CMS::$injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
	} elseif (ACP3\Core\Validate::formToken() === false) {
		ACP3\CMS::$injector['View']->setContent(Core\Functions::errorBox(ACP3\CMS::$injector['Lang']->t('system', 'form_already_submitted')));
	} else {
		$data = array(
			'width' => (int) $_POST['width'],
			'height' => (int) $_POST['height'],
			'filesize' => (int) $_POST['filesize'],
		);
		$bool = ACP3\Core\Config::setSettings('emoticons', $data);

		ACP3\CMS::$injector['Session']->unsetFormToken();

		ACP3\Core\Functions::setRedirectMessage($bool, ACP3\CMS::$injector['Lang']->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/emoticons');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	$settings = ACP3\Core\Config::getSettings('emoticons');

	ACP3\CMS::$injector['View']->assign('form', isset($_POST['submit']) ? $_POST : $settings);

	ACP3\CMS::$injector['Session']->generateFormToken();
}
