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

/**
 * Class Helpers
 * @package ACP3\Modules\Captcha
 */
class Helpers
{
    /**
     * @var Core\Auth
     */
    protected $auth;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $securityHelper;
    /**
     * @var Core\URI
     */
    protected $uri;
    /**
     * @var Core\View
     */
    protected $view;

    public function __construct(Core\Auth $auth, Core\URI $uri, Core\View $view, Core\Helpers\Secure $securityHelper)
    {
        $this->auth = $auth;
        $this->uri = $uri;
        $this->view = $view;
        $this->securityHelper = $securityHelper;
    }

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
    public function captcha($captchaLength = 5, $id = 'captcha', $inputOnly = false, $path = '')
    {
        // Wenn man als User angemeldet ist, Captcha nicht anzeigen
        if ($this->auth->isUser() === false) {
            $path = sha1($this->uri->route(empty($path) === true ? $this->uri->query : $path));

            $_SESSION['captcha_' . $path] = $this->securityHelper->salt($captchaLength);

            $captcha = array();
            $captcha['width'] = $captchaLength * 25;
            $captcha['id'] = $id;
            $captcha['height'] = 30;
            $captcha['input_only'] = $inputOnly;
            $captcha['path'] = $path;
            $this->view->assign('captcha', $captcha);
            return $this->view->fetchTemplate('captcha/captcha.tpl');
        }
        return '';
    }

}