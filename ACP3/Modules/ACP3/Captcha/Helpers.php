<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Captcha;

use ACP3\Core;

/**
 * @deprecated Since 4.8.0, to be removed with version 5.0.0
 */
class Helpers
{
    const CAPTCHA_DEFAULT_LENGTH = 5;
    const CAPTCHA_DEFAULT_INPUT_ID = 'captcha';

    /**
     * @var \ACP3\Modules\ACP3\Users\Model\UserModel
     */
    protected $user;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;
    /**
     * @var \ACP3\Core\Router\RouterInterface
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
     * @param \ACP3\Core\Http\RequestInterface           $request
     * @param \ACP3\Core\Router\RouterInterface          $router
     * @param \ACP3\Core\Session\SessionHandlerInterface $sessionHandler
     * @param \ACP3\Core\View                            $view
     * @param \ACP3\Core\Helpers\Secure                  $secureHelper
     */
    public function __construct(
        \ACP3\Modules\ACP3\Users\Model\UserModel $user,
        Core\Http\RequestInterface $request,
        Core\Router\RouterInterface $router,
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
     * Erzeugt das Captchafeld für das Template.
     *
     * @param int    $captchaLength
     * @param string $formFieldId
     * @param bool   $inputOnly
     * @param string $path
     *
     * @return string
     */
    public function captcha(
        $captchaLength = self::CAPTCHA_DEFAULT_LENGTH,
        $formFieldId = self::CAPTCHA_DEFAULT_INPUT_ID,
        $inputOnly = false,
        $path = ''
    ) {
        if ($this->user->isAuthenticated() === false) {
            $path = \sha1($this->router->route(empty($path) === true ? $this->request->getQuery() : $path));

            $this->sessionHandler->set('captcha_' . $path, $this->secureHelper->salt($captchaLength));

            $this->view->assign('captcha', [
                'width' => $captchaLength * 25,
                'id' => $formFieldId,
                'height' => 30,
                'input_only' => $inputOnly,
                'path' => $path,
            ]);

            return $this->view->fetchTemplate('Captcha/Partials/captcha_native.tpl');
        }

        return '';
    }
}
