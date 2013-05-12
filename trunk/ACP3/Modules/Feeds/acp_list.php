<?php
/**
 * Feeds
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (isset($_POST['submit']) === true) {
	if (empty($_POST['feed_type']) || in_array($_POST['feed_type'], array('RSS 1.0', 'RSS 2.0', 'ATOM')) === false)
		$errors['mail'] = ACP3\CMS::$injector['Lang']->t('feeds', 'select_feed_type');

	if (isset($errors) === true) {
		ACP3\CMS::$injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
	} elseif (ACP3\Core\Validate::formToken() === false) {
		ACP3\CMS::$injector['View']->setContent(Core\Functions::errorBox(ACP3\CMS::$injector['Lang']->t('system', 'form_already_submitted')));
	} else {
		$data = array(
			'feed_image' => ACP3\Core\Functions::str_encode($_POST['feed_image']),
			'feed_type' => $_POST['feed_type']
		);

		$bool = ACP3\Core\Config::setSettings('feeds', $data);

		ACP3\CMS::$injector['Session']->unsetFormToken();

		ACP3\Core\Functions::setRedirectMessage($bool, ACP3\CMS::$injector['Lang']->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/feeds');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	ACP3\Core\Functions::getRedirectMessage();

	$settings = ACP3\Core\Config::getSettings('feeds');

	$feed_type = array(
		'RSS 1.0',
		'RSS 2.0',
		'ATOM'
	);
	ACP3\CMS::$injector['View']->assign('feed_types', ACP3\Core\Functions::selectGenerator('feed_type', $feed_type, $feed_type, $settings['feed_type']));

	ACP3\CMS::$injector['View']->assign('form', isset($_POST['submit']) ? $_POST : $settings);

	ACP3\CMS::$injector['Session']->generateFormToken();
}
