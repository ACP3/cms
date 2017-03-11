<?php
/**
 * Copyright (c) 2017 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Captcha\Extension;

use ACP3\Core;
use ACP3\Core\I18n\Translator;

class NativeCaptchaExtension implements CaptchaExtensionInterface
{
    /**
     * @var Translator
     */
    private $translator;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    private $secureHelper;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\Router\RouterInterface
     */
    private $router;
    /**
     * @var \ACP3\Core\Session\SessionHandlerInterface
     */
    private $sessionHandler;
    /**
     * @var \ACP3\Core\View
     */
    private $view;
    /**
     * @var \ACP3\Modules\ACP3\Users\Model\UserModel
     */
    private $user;

    /**
     * NativeCaptchaExtension constructor.
     * @param Translator $translator
     * @param \ACP3\Modules\ACP3\Users\Model\UserModel $user
     * @param Core\Http\RequestInterface $request
     * @param Core\Router\RouterInterface $router
     * @param Core\Session\SessionHandlerInterface $sessionHandler
     * @param Core\View $view
     * @param Core\Helpers\Secure $secureHelper
     */
    public function __construct(
        Translator $translator,
        Core\Http\RequestInterface $request,
        Core\Router\RouterInterface $router,
        Core\Session\SessionHandlerInterface $sessionHandler,
        Core\View $view,
        Core\Helpers\Secure $secureHelper,
        \ACP3\Modules\ACP3\Users\Model\UserModel $user
    ) {
        $this->translator = $translator;
        $this->request = $request;
        $this->router = $router;
        $this->sessionHandler = $sessionHandler;
        $this->view = $view;
        $this->secureHelper = $secureHelper;
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getCaptchaName()
    {
        return $this->translator->t('captcha', 'native');
    }

    /**
     * @inheritdoc
     */
    public function getCaptcha(
        $captchaLength = self::CAPTCHA_DEFAULT_LENGTH,
        $formFieldId = self::CAPTCHA_DEFAULT_INPUT_ID,
        $inputOnly = false,
        $path = ''
    ) {
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
            return $this->view->fetchTemplate('Captcha/Partials/captcha_native.tpl');
        }
        return '';
    }

    /**
     * @inheritdoc
     */
    public function getValidationRule()
    {
        return '';
    }
}
