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

abstract class Helpers
{

    /**
     * Erzeugt das Captchafeld für das Template
     *
     * @param integer $captchaLength
     *  Anzahl der Zeichen, welche das Captcha haben soll
     * @param string $id
     * @param bool $inputOnly
     * @param string $path
     * @return string
     */
    public static function captcha($captchaLength = 5, $id = 'captcha', $inputOnly = false, $path = '')
    {
        // Wenn man als User angemeldet ist, Captcha nicht anzeigen
        if (Core\Registry::get('Auth')->isUser() === false) {
            $uri = Core\Registry::get('URI');
            $path = sha1($uri->route(empty($path) === true ? $uri->query : $path));

            $securityHelper = new Core\Helpers\Secure();

            $_SESSION['captcha_' . $path] = $securityHelper->salt($captchaLength);

            $captcha = array();
            $captcha['width'] = $captchaLength * 25;
            $captcha['id'] = $id;
            $captcha['height'] = 30;
            $captcha['input_only'] = $inputOnly;
            $captcha['path'] = $path;
            Core\Registry::get('View')->assign('captcha', $captcha);
            return Core\Registry::get('View')->fetchTemplate('captcha/captcha.tpl');
        }
        return '';
    }

}
