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
		$errors['mail'] = ACP3_CMS::$lang->t('feeds', 'select_feed_type');

	if (isset($errors) === true) {
		ACP3_CMS::$view->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_CMS::$view->setContent(errorBox(ACP3_CMS::$lang->t('system', 'form_already_submitted')));
	} else {
		$data = array(
			'feed_image' => str_encode($_POST['feed_image']),
			'feed_type' => $_POST['feed_type']
		);

		$bool = ACP3_Config::setSettings('feeds', $data);

		ACP3_CMS::$session->unsetFormToken();

		setRedirectMessage($bool, ACP3_CMS::$lang->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/feeds');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	getRedirectMessage();

	$settings = ACP3_Config::getSettings('feeds');

	$feed_type = array(
		'RSS 1.0',
		'RSS 2.0',
		'ATOM'
	);
	ACP3_CMS::$view->assign('feed_types', selectGenerator('feed_type', $feed_type, $feed_type, $settings['feed_type']));

	ACP3_CMS::$view->assign('form', isset($_POST['submit']) ? $_POST : $settings);

	ACP3_CMS::$session->generateFormToken();
}
