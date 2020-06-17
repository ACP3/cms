<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Modules\ACP3\Users;
use Symfony\Component\HttpFoundation\JsonResponse;

class Login extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var Users\Model\AuthenticationModel
     */
    private $authenticationModel;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    private $secureHelper;
    /**
     * @var \ACP3\Core\Http\RedirectResponse
     */
    private $redirectResponse;
    /**
     * @var \ACP3\Modules\ACP3\Users\ViewProviders\LoginViewProvider
     */
    private $loginViewProvider;
    /**
     * @var \ACP3\Core\Authentication\Model\UserModelInterface
     */
    private $user;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        UserModelInterface $user,
        Core\Http\RedirectResponse $redirectResponse,
        Core\Helpers\Secure $secureHelper,
        Users\ViewProviders\LoginViewProvider $loginViewProvider,
        Users\Model\AuthenticationModel $authenticationModel
    ) {
        parent::__construct($context);

        $this->authenticationModel = $authenticationModel;
        $this->secureHelper = $secureHelper;
        $this->redirectResponse = $redirectResponse;
        $this->loginViewProvider = $loginViewProvider;
        $this->user = $user;
    }

    /**
     * @return array|JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute()
    {
        if ($this->user->isAuthenticated() === true) {
            return $this->redirectResponse->toNewPage($this->appPath->getWebRoot());
        }

        return ($this->loginViewProvider)();
    }

    /**
     * @return array|JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function executePost()
    {
        try {
            $this->authenticationModel->login(
                $this->secureHelper->strEncode($this->request->getPost()->get('nickname', '')),
                $this->request->getPost()->get('pwd', ''),
                $this->request->getPost()->has('remember')
            );

            if ($this->request->getParameters()->has('redirect')) {
                return $this->redirectResponse->temporary(
                    \base64_decode($this->request->getParameters()->get('redirect'))
                );
            }

            return $this->redirectResponse->toNewPage($this->appPath->getWebRoot());
        } catch (Users\Exception\LoginFailedException $e) {
            $phrase = $this->translator->t('users', 'nickname_or_password_wrong');
        } catch (Users\Exception\UserAccountLockedException $e) {
            $phrase = $this->translator->t('users', 'account_locked');
        }

        $localizedException = new Core\Authentication\Exception\AuthenticationException($phrase);

        return $this->actionHelper->renderErrorBoxOnFailedFormValidation($localizedException);
    }
}
