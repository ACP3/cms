<?php
/**
 * Newsletter
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */
function subscribeToNewsletter($emailAddress)
{
	global $lang, $uri;

	$hash = md5(mt_rand(0, microtime(true)));
	$host = htmlentities($_SERVER['HTTP_HOST']);
	$settings = config::getModuleSettings('newsletter');

	$subject = sprintf($lang->t('newsletter', 'subscribe_mail_subject'), CONFIG_SEO_TITLE);
	$body = str_replace('{host}', $host, $lang->t('newsletter', 'subscribe_mail_body')) . "\n\n";
	$body.= 'http://' . $host . $uri->route('newsletter/activate/hash_' . $hash . '/mail_' . $emailAddress);
	$mail_sent = genEmail('', $emailAddress, $settings['mail'], $subject, $body);

	// Newsletter-Konto nur erstellen, wenn die E-Mail erfolgreich versendet werden konnte
	if ($mail_sent) {
		global $db;

		$insert_values = array('id' => '', 'mail' => $emailAddress, 'hash' => $hash);
		$bool = $db->insert('newsletter_accounts', $insert_values);
	}

	return $mail_sent && isset($bool) && $bool;
}