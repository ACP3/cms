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
		$errors['mail'] = $lang->t('feeds', 'select_feed_type');

	if (isset($errors) === true) {
		$tpl->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_View::setContent(errorBox($lang->t('common', 'form_already_submitted')));
	} else {
		$settings = array(
			'feed_image' => $db->escape($_POST['feed_image']),
			'feed_type' => $db->escape($_POST['feed_type'])
		);

		$bool = ACP3_Config::setSettings('feeds', $settings);

		$session->unsetFormToken();

		ACP3_View::setContent(confirmBox($bool === true ? $lang->t('common', 'settings_success') : $lang->t('common', 'settings_error'), $uri->route('acp/feeds')));
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	$settings = ACP3_Config::getSettings('feeds');
	$settings['feed_image'] = $db->escape($settings['feed_image'], 3);
	$settings['feed_type'] = $db->escape($settings['feed_type'], 3);

	$feed_types = array();
	$feed_types[0]['value'] = 'RSS 1.0';
	$feed_types[0]['selected'] = selectEntry('feed_type', $feed_types[0]['value'], $settings['feed_type']);
	$feed_types[1]['value'] = 'RSS 2.0';
	$feed_types[1]['selected'] = selectEntry('feed_type', $feed_types[1]['value'], $settings['feed_type']);
	$feed_types[2]['value'] = 'ATOM';
	$feed_types[2]['selected'] = selectEntry('feed_type', $feed_types[2]['value'], $settings['feed_type']);
	$tpl->assign('feed_types', $feed_types);

	$tpl->assign('form', isset($_POST['submit']) ? $_POST : $settings);

	$session->generateFormToken();

	ACP3_View::setContent(ACP3_View::fetchTemplate('feeds/acp_list.tpl'));
}
