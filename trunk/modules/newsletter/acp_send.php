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

if (ACP3_Validate::isNumber(ACP3_CMS::$uri->id) === true &&
	ACP3_CMS::$db2->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'newsletter_archive WHERE id = ?', array(ACP3_CMS::$uri->id)) == 1) {
	$settings = ACP3_Config::getSettings('newsletter');
	$newsletter = ACP3_CMS::$db2->fetchAssoc('SELECT subject, text FROM ' . DB_PRE . 'newsletter_archive WHERE id = ?', array(ACP3_CMS::$uri->id));

	$subject = $newsletter['subject'];
	$body = html_entity_decode($newsletter['text'] . "\n-- \n" . $settings['mailsig'], ENT_QUOTES, 'UTF-8');

	require_once MODULES_DIR . 'newsletter/functions.php';
	$bool = sendNewsletter($subject, $body, $settings['mail']);
	$bool2 = false;
	if ($bool === true) {
		$bool2 = ACP3_CMS::$db2->update(DB_PRE . 'newsletter_archive', array('status' => '1'), array('id' => ACP3_CMS::$uri->id));
	}

	setRedirectMessage($bool && $bool2, ACP3_CMS::$lang->t('newsletter', $bool === true && $bool2 !== false ? 'create_success' : 'create_save_error'), 'acp/newsletter');
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}