<?php
/**
 * Newsletter
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit;

/**
 * Meldet eine E-Mail-Adresse beim Newsletter an
 *
 * @param string $emailAddress
 *	Die anzumeldente E-Mail-Adresse
 * @return boolean
 */
function subscribeToNewsletter($emailAddress)
{
	global $lang, $uri;

	$hash = md5(mt_rand(0, microtime(true)));
	$host = htmlentities($_SERVER['HTTP_HOST']);
	$settings = ACP3_Config::getModuleSettings('newsletter');

	$subject = sprintf($lang->t('newsletter', 'subscribe_mail_subject'), CONFIG_SEO_TITLE);
	$body = str_replace('{host}', $host, $lang->t('newsletter', 'subscribe_mail_body')) . "\n\n";
	$body.= 'http://' . $host . $uri->route('newsletter/activate/hash_' . $hash . '/mail_' . $emailAddress);
	$mail_sent = generateEmail('', $emailAddress, $settings['mail'], $subject, $body);

	// Newsletter-Konto nur erstellen, wenn die E-Mail erfolgreich versendet werden konnte
	if ($mail_sent === true) {
		global $db;

		$insert_values = array('id' => '', 'mail' => $emailAddress, 'hash' => $hash);
		$bool = $db->insert('newsletter_accounts', $insert_values);
	}

	return $mail_sent === true && isset($bool) && $bool !== false;
}