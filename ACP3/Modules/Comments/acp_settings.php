<?php
/**
 * Comments
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

$emoticons_active = ACP3\Core\Modules::isActive('emoticons');

if (isset($_POST['submit']) === true) {
	if (empty($_POST['dateformat']) || ($_POST['dateformat'] !== 'long' && $_POST['dateformat'] !== 'short'))
		$errors['dateformat'] = ACP3\CMS::$injector['Lang']->t('system', 'select_date_format');
	if ($emoticons_active === true && (!isset($_POST['emoticons']) || ($_POST['emoticons'] != 0 && $_POST['emoticons'] != 1)))
		$errors[] = ACP3\CMS::$injector['Lang']->t('comments', 'select_emoticons');

	if (isset($errors) === true) {
		ACP3\CMS::$injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
	} elseif (ACP3\Core\Validate::formToken() === false) {
		ACP3\CMS::$injector['View']->setContent(Core\Functions::errorBox(ACP3\CMS::$injector['Lang']->t('system', 'form_already_submitted')));
	} else {
		$data = array(
			'dateformat' => ACP3\Core\Functions::str_encode($_POST['dateformat']),
			'emoticons' => $_POST['emoticons'],
		);
		$bool = ACP3\Core\Config::setSettings('comments', $data);

		ACP3\CMS::$injector['Session']->unsetFormToken();

		ACP3\Core\Functions::setRedirectMessage($bool, ACP3\CMS::$injector['Lang']->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/comments');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	$settings = ACP3\Core\Config::getSettings('comments');

	ACP3\CMS::$injector['View']->assign('dateformat', ACP3\CMS::$injector['Date']->dateformatDropdown($settings['dateformat']));

	// Emoticons erlauben
	if ($emoticons_active === true) {
		$lang_allow_emoticons = array(ACP3\CMS::$injector['Lang']->t('system', 'yes'), ACP3\CMS::$injector['Lang']->t('system', 'no'));
		ACP3\CMS::$injector['View']->assign('allow_emoticons', ACP3\Core\Functions::selectGenerator('emoticons', array(1, 0), $lang_allow_emoticons, $settings['emoticons'], 'checked'));
	}

	ACP3\CMS::$injector['Session']->generateFormToken();
}