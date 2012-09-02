<?php
/**
 * Captcha
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit();

/**
 * Erzeugt das Captchafeld für das Template
 *
 * @param integer $captcha_length
 *  Anzahl der Zeichen, welche das Captcha haben soll
 * @return string
 */
function captcha($captcha_length = 5)
{
	global $auth, $tpl;

	// Wenn man als User angemeldet ist, Captcha nicht anzeigen
	if (ACP3_CMS::$auth->isUser() === false) {
		$_SESSION['captcha'] = salt($captcha_length);

		$captcha = array();
		$captcha['width'] = $captcha_length * 25;
		$captcha['height'] = 30;
		ACP3_CMS::$view->assign('captcha', $captcha);
		return ACP3_CMS::$view->fetchTemplate('captcha/captcha.tpl');
	}
	return '';
}