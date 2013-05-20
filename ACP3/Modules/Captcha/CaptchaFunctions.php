<?php

/**
 * Captcha
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

namespace ACP3\Modules\Captcha;

use ACP3\Core;

abstract class CaptchaFunctions {

	/**
	 * Erzeugt das Captchafeld für das Template
	 *
	 * @param integer $captcha_length
	 *  Anzahl der Zeichen, welche das Captcha haben soll
	 * @return string
	 */
	public static function captcha($captcha_length = 5, $id = 'captcha')
	{
		// Wenn man als User angemeldet ist, Captcha nicht anzeigen
		if (Core\Registry::get('Auth')->isUser() === false) {
			$_SESSION['captcha'] = Core\Functions::salt($captcha_length);

			$captcha = array();
			$captcha['width'] = $captcha_length * 25;
			$captcha['id'] = $id;
			$captcha['height'] = 30;
			Core\Registry::get('View')->assign('captcha', $captcha);
			return Core\Registry::get('View')->fetchTemplate('captcha/captcha.tpl');
		}
		return '';
	}

}
