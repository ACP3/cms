<?php
/**
 * Newsletter
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (validate::isNumber($uri->id) && $db->countRows('*', 'newsletter_archive', 'id = \'' . $uri->id . '\'') == '1') {
	$settings = config::getModuleSettings('newsletter');
	$newsletter = $db->select('subject, text', 'newsletter_archive', 'id = \'' . $uri->id . '\'');
	$accounts = $db->select('mail', 'newsletter_accounts', 'hash = \'\'');
	$c_accounts = count($accounts);
	$bool = true;
	$bool2 = true;

	$subject = $db->escape($newsletter[0]['subject'], 3);
	$body = html_entity_decode($db->escape($newsletter[0]['text'], 3) . "\n" . $db->escape($settings['mailsig'], 3), ENT_QUOTES, 'UTF-8');

	for ($i = 0; $i < $c_accounts; ++$i) {
		$bool = genEmail('', $accounts[$i]['mail'], $settings['mail'], $subject, $body);
		if (!$bool)
			break;
	}
	if ($bool) {
		$bool2 = $db->update('newsletter_archive', array('status' => '1'), 'id = \'' . $uri->id . '\'');
	}

	view::setContent(comboBox($bool && $bool2 !== null ? $lang->t('newsletter', 'compose_success') : $lang->t('newsletter', 'compose_save_error'), $uri->route('acp/newsletter/adm_list_archive')));
} else {
	$uri->redirect('errors/404');
}
