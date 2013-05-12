<?php
/**
 * Files
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

$comments_active = ACP3\Core\Modules::isActive('comments');

if (isset($_POST['submit']) === true) {
	if (empty($_POST['dateformat']) || ($_POST['dateformat'] !== 'long' && $_POST['dateformat'] !== 'short'))
		$errors['dateformat'] = ACP3\CMS::$injector['Lang']->t('system', 'select_date_format');
	if (ACP3\Core\Validate::isNumber($_POST['sidebar']) === false)
		$errors['sidebar'] = ACP3\CMS::$injector['Lang']->t('system', 'select_sidebar_entries');
	if ($comments_active === true && (!isset($_POST['comments']) || $_POST['comments'] != 1 && $_POST['comments'] != 0))
		$errors[] = ACP3\CMS::$injector['Lang']->t('files', 'select_allow_comments');

	if (isset($errors) === true) {
		ACP3\CMS::$injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
	} elseif (ACP3\Core\Validate::formToken() === false) {
		ACP3\CMS::$injector['View']->setContent(Core\Functions::errorBox(ACP3\CMS::$injector['Lang']->t('system', 'form_already_submitted')));
	} else {
		$data = array(
			'dateformat' => ACP3\Core\Functions::str_encode($_POST['dateformat']),
			'sidebar' => (int) $_POST['sidebar'],
			'comments' => $_POST['comments']
		);
		$bool = ACP3\Core\Config::setSettings('files', $data);

		ACP3\CMS::$injector['Session']->unsetFormToken();

		ACP3\Core\Functions::setRedirectMessage($bool, ACP3\CMS::$injector['Lang']->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/files');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	$settings = ACP3\Core\Config::getSettings('files');

	if ($comments_active === true) {
		$lang_comments = array(ACP3\CMS::$injector['Lang']->t('system', 'yes'), ACP3\CMS::$injector['Lang']->t('system', 'no'));
		ACP3\CMS::$injector['View']->assign('comments', ACP3\Core\Functions::selectGenerator('comments', array(1, 0), $lang_comments, $settings['comments'], 'checked'));
	}

	ACP3\CMS::$injector['View']->assign('dateformat', ACP3\CMS::$injector['Date']->dateformatDropdown($settings['dateformat']));

	ACP3\CMS::$injector['View']->assign('sidebar_entries', ACP3\Core\Functions::recordsPerPage((int) $settings['sidebar'], 1, 10));

	ACP3\CMS::$injector['Session']->generateFormToken();
}