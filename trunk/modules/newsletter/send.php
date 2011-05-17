<?php
/**
 * Newsletter
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

if (validate::isNumber($uri->id) && $db->countRows('*', 'newsletter_archive', 'id = \'' . $uri->id . '\'') == '1') {
	$settings = config::output('newsletter');
	$newsletter = $db->select('subject, text', 'newsletter_archive', 'id = \'' . $uri->id . '\'');
	$accounts = $db->select('mail', 'newsletter_accounts', 'hash = \'\'');
	$c_accounts = count($accounts);
	$bool = true;
	$bool2 = true;

	$subject = $db->escape($newsletter[0]['subject'], 3);
	$body = $db->escape($newsletter[0]['text'], 3) . "\n" . $settings['mailsig'];

	for ($i = 0; $i < $c_accounts; ++$i) {
		$bool = genEmail('', $accounts[$i]['mail'], $settings['mail'], $subject, $body);
		if (!$bool)
			break;
	}
	if ($bool) {
		$bool2 = $db->update('newsletter_archive', array('status' => '1'), 'id = \'' . $uri->id . '\'');
	}

	$content = comboBox($bool && $bool2 !== null ? $lang->t('newsletter', 'compose_success') : $lang->t('newsletter', 'compose_save_error'), uri('acp/newsletter/adm_list_archive'));
} else {
	redirect('errors/404');
}
