<?php
/**
 * Newsletter
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (ACP3_Validate::isNumber(ACP3_CMS::$uri->id) === true && ACP3_CMS::$db->countRows('*', 'newsletter_archive', 'id = \'' . ACP3_CMS::$uri->id . '\'') == 1) {
	$settings = ACP3_Config::getSettings('newsletter');
	$newsletter = ACP3_CMS::$db->select('subject, text', 'newsletter_archive', 'id = \'' . ACP3_CMS::$uri->id . '\'');
	$accounts = ACP3_CMS::$db->select('mail', 'newsletter_accounts', 'hash = \'\'');
	$c_accounts = count($accounts);
	$bool = $bool2 = false;

	$subject = ACP3_CMS::$db->escape($newsletter[0]['subject'], 3);
	$body = html_entity_decode(ACP3_CMS::$db->escape($newsletter[0]['text'], 3) . "\n-- \n" . ACP3_CMS::$db->escape($settings['mailsig'], 3), ENT_QUOTES, 'UTF-8');

	for ($i = 0; $i < $c_accounts; ++$i) {
		$bool = generateEmail('', $accounts[$i]['mail'], $settings['mail'], $subject, $body);
		if ($bool === false)
			break;
	}
	if ($bool === true) {
		$bool2 = ACP3_CMS::$db->update('newsletter_archive', array('status' => '1'), 'id = \'' . ACP3_CMS::$uri->id . '\'');
	}

	setRedirectMessage($bool && $bool2, ACP3_CMS::$lang->t('newsletter', $bool === true && $bool2 !== false ? 'compose_success' : 'compose_save_error'), 'acp/newsletter/list_archive');
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}