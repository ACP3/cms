<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Captcha;

use ACP3\Core;

/**
 * Class Helpers
 * @package ACP3\Modules\ACP3\Captcha
 */
class Helpers
{
    const CAPTCHA_DEFAULT_LENGTH = 5;
    const CAPTCHA_DEFAULT_INPUT_ID = 'captcha';

    /**
     * @var \ACP3\Core\User
     */
    protected $user;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var \ACP3\Core\Http\Request
     */
    protected $request;
    /**
     * @var \ACP3\Core\RouterInterface
     */
    protected $router;
    /**
     * @var \ACP3\Core\Session\SessionHandlerInterface
     */
    protected $sessionHandler;
    /**
     * @var \ACP3\Core\View
     */
    protected $view;

    /**
     * Helpers constructor.
     *
     * @param \ACP3\Core\User                            $user
     * @param \ACP3\Core\Http\Request                    $request
     * @param \ACP3\Core\RouterInterface                 $router
     * @param \ACP3\Core\Session\SessionHandlerInterface $sessionHandler
     * @param \ACP3\Core\View                            $view
     * @param \ACP3\Core\Helpers\Secure                  $secureHelper
     */
    public function __construct(
        Core\User $user,
        Core\Http\Request $request,
        Core\RouterInterface $router,
        Core\Session\SessionHandlerInterface $sessionHandler,
        Core\View $view,
        Core\Helpers\Secure $secureHelper
    ) {
        $this->user = $user;
        $this->request = $request;
        $this->router = $router;
        $this->sessionHandler = $sessionHandler;
        $this->view = $view;
        $this->secureHelper = $secureHelper;
    }

    /**
     * Erzeugt das Captchafeld für das Template
     *
     * @param integer $captchaLength
     * @param string  $formFieldId
     * @param bool    $inputOnly
     * @param string  $path
     *
     * @return string
     */
    public function captcha(
        $captchaLength = self::CAPTCHA_DEFAULT_LENGTH,
        $formFieldId = self::CAPTCHA_DEFAULT_INPUT_ID,
        $inputOnly = false,
        $path = ''
    ) {
        // Wenn man als User angemeldet ist, Captcha nicht anzeigen
        if ($this->user->isAuthenticated() === false) {
            $path = sha1($this->router->route(empty($path) === true ? $this->request->getQuery() : $path));

            $this->sessionHandler->set('captcha_' . $path, $this->secureHelper->salt($captchaLength));

            $this->view->assign('captcha', [
                'width' => $captchaLength * 25,
                'id' => $formFieldId,
                'height' => 30,
                'input_only' => $inputOnly,
                'path' => $path
            ]);
            return $this->view->fetchTemplate('captcha/captcha.tpl');
        }
        return '';
    }
}
