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

if (validate::isNumber($uri->id) && $db->select('COUNT(id)', 'newsletter_archive', 'id = \'' . $uri->id . '\'', 0, 0, 0, 1) == '1') {
	$settings = config::output('newsletter');
	$newsletter = $db->select('subject, text', 'newsletter_archive', 'id = \'' . $uri->id . '\'');
	$accounts = $db->select('mail', 'newsletter_accounts', 'hash = \'\'');
	$c_accounts = count($accounts);
	$bool = true;
	$bool2 = true;

	for ($i = 0; $i < $c_accounts; ++$i) {
		$bool = @mail($accounts[$i]['mail'], $db->escape($newsletter[0]['subject'], 3), $db->escape($newsletter[0]['text'], 3) . $settings['mailsig'], 'FROM:' . $settings['mail'] . "\r\n" . 'Content-Type: text/plain; charset: UTF-8');
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
?>