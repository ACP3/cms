<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Helpers\FormAction;
use ACP3\Modules\ACP3\Users;
use Symfony\Component\HttpFoundation\Response;

class LoginPost extends Core\Controller\AbstractWidgetAction
{
    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        private ApplicationPath $applicationPath,
        private FormAction $actionHelper,
        private Core\Http\RedirectResponse $redirectResponse,
        private Core\Helpers\Secure $secureHelper,
        private Users\Model\AuthenticationModel $authenticationModel
    ) {
        parent::__construct($context);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(): Response|\Symfony\Component\HttpFoundation\RedirectResponse
    {
        try {
            $this->authenticationModel->login(
                $this->secureHelper->strEncode($this->request->getPost()->get('nickname', '')),
                $this->request->getPost()->get('pwd', ''),
                $this->request->getPost()->has('remember')
            );

            if ($this->request->getParameters()->has('redirect')) {
                return $this->redirectResponse->temporary(
                    base64_decode($this->request->getParameters()->get('redirect'))
                );
            }

            return $this->redirectResponse->toNewPage($this->applicationPath->getWebRoot());
        } catch (Users\Exception\LoginFailedException) {
            $phrase = $this->translator->t('users', 'nickname_or_password_wrong');
        } catch (Users\Exception\UserAccountLockedException) {
            $phrase = $this->translator->t('users', 'account_locked');
        }

        $localizedException = new Core\Authentication\Exception\AuthenticationException($phrase);

        return $this->actionHelper->renderErrorBoxOnFailedFormValidation($localizedException);
    }
}
