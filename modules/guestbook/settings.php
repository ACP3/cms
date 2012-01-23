<?php
/**
 * Guestbook
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

$emoticons = modules::isActive('emoticons');
$newsletter = modules::isActive('newsletter');

if (isset($_POST['form'])) {
	$form = $_POST['form'];

	if (empty($form['dateformat']) || ($form['dateformat'] != 'long' && $form['dateformat'] != 'short'))
		$errors[] = $lang->t('common', 'select_date_format');
	if (!isset($form['notify']) || ($form['notify'] != 0 && $form['notify'] != 1 && $form['notify'] != 2))
		$errors[] = $lang->t('guestbook', 'select_notification_type');
	if (!validate::email($form['notify_email']))
		$errors[] = $lang->t('common', 'wrong_email_format');
	if ($emoticons && (!isset($form['notify']) || ($form['notify'] != 0 && $form['notify'] != 1)))
		$errors[] = $lang->t('guestbook', 'select_emoticons');
	if ($newsletter && (!isset($form['newsletter_integration']) || ($form['newsletter_integration'] != 0 && $form['newsletter_integration'] != 1)))
		$errors[] = $lang->t('guestbook', 'select_newsletter_integration');

	if (isset($errors)) {
		$tpl->assign('error_msg', comboBox($errors));
	} else {
		$bool = config::module('guestbook', $form);

		view::setContent(comboBox($bool ? $lang->t('common', 'settings_success') : $lang->t('common', 'settings_error'), $uri->route('acp/guestbook')));
	}
}
if (!isset($_POST['form']) || isset($errors) && is_array($errors)) {
	$settings = config::getModuleSettings('guestbook');

	$dateformat[0]['value'] = 'short';
	$dateformat[0]['selected'] = selectEntry('dateformat', 'short', $settings['dateformat']);
	$dateformat[0]['lang'] = $lang->t('common', 'date_format_short');
	$dateformat[1]['value'] = 'long';
	$dateformat[1]['selected'] = selectEntry('dateformat', 'long', $settings['dateformat']);
	$dateformat[1]['lang'] = $lang->t('common', 'date_format_long');
	$tpl->assign('dateformat', $dateformat);

	// Emoticons erlauben
	if ($emoticons) {
		$allow_emoticons[0]['value'] = '1';
		$allow_emoticons[0]['checked'] = selectEntry('emoticons', '1', $settings['emoticons'], 'checked');
		$allow_emoticons[0]['lang'] = $lang->t('common', 'yes');
		$allow_emoticons[1]['value'] = '0';
		$allow_emoticons[1]['checked'] = selectEntry('emoticons', '0', $settings['emoticons'], 'checked');
		$allow_emoticons[1]['lang'] = $lang->t('common', 'no');
		$tpl->assign('allow_emoticons', $allow_emoticons);
	}

	// In Newsletter integrieren
	if ($newsletter) {
		$newsletter_integration[0]['value'] = '1';
		$newsletter_integration[0]['checked'] = selectEntry('newsletter_integration', '1', $settings['newsletter_integration'], 'checked');
		$newsletter_integration[0]['lang'] = $lang->t('common', 'yes');
		$newsletter_integration[1]['value'] = '0';
		$newsletter_integration[1]['checked'] = selectEntry('newsletter_integration', '0', $settings['newsletter_integration'], 'checked');
		$newsletter_integration[1]['lang'] = $lang->t('common', 'no');
		$tpl->assign('newsletter_integration', $newsletter_integration);
	}

	$notify[0]['value'] = '0';
	$notify[0]['selected'] = selectEntry('notify', '0', $settings['notify']);
	$notify[0]['lang'] = $lang->t('guestbook', 'no_notification');
	$notify[1]['value'] = '1';
	$notify[1]['selected'] = selectEntry('notify', '1', $settings['notify']);
	$notify[1]['lang'] = $lang->t('guestbook', 'notify_on_new_entry');
	$notify[2]['value'] = '2';
	$notify[2]['selected'] = selectEntry('notify', '2', $settings['notify']);
	$notify[2]['lang'] = $lang->t('guestbook', 'notify_and_enable');
	$tpl->assign('notify', $notify);

	$tpl->assign('form', isset($form) ? $form : array('notify_email' => $settings['notify_email']));

	view::setContent(view::fetchTemplate('guestbook/settings.tpl'));
}