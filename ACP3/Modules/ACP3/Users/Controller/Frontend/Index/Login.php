<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Captcha;
use ACP3\Modules\ACP3\Permissions;
use ACP3\Modules\ACP3\Users;

/**
 * Class Login
 * @package ACP3\Modules\ACP3\Users\Controller\Frontend\Index
 */
class Login extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var Users\Model\AuthenticationModel
     */
    protected $authenticationModel;

    /**
     * Login constructor.
     * @param Core\Controller\Context\FrontendContext $context
     * @param Users\Model\AuthenticationModel $authenticationModel
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Users\Model\AuthenticationModel $authenticationModel)
    {
        parent::__construct($context);

        $this->authenticationModel = $authenticationModel;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute()
    {
        if ($this->user->isAuthenticated() === true) {
            return $this->redirect()->toNewPage($this->appPath->getWebRoot());
        } elseif ($this->request->getPost()->count() !== 0) {
            return $this->executePost();
        }
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost()
    {
        try {
            $this->authenticationModel->login(
                $this->get('core.helpers.secure')->strEncode($this->request->getPost()->get('nickname', '')),
                $this->request->getPost()->get('pwd', ''),
                $this->request->getPost()->has('remember')
            );

            if ($this->request->getParameters()->has('redirect')) {
                return $this->redirect()->temporary(
                    base64_decode($this->request->getParameters()->get('redirect'))
                );
            }

            return $this->redirect()->toNewPage($this->appPath->getWebRoot());
        } catch (Users\Exception\LoginFailedException $e) {
            $errorPhrase = 'nickname_or_password_wrong';
        } catch (Users\Exception\UserAccountLockedException $e) {
            $errorPhrase = 'account_locked';
        }

        return [
            'error_msg' => $this->get('core.helpers.alerts')->errorBox($this->translator->t('users', $errorPhrase))
        ];
    }
}
