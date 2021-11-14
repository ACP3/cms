<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Modules\ACP3\Users;

class Logout extends Core\Controller\AbstractWidgetAction
{
    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        private UserModelInterface $user,
        private Core\Http\RedirectResponse $redirectResponse,
        private Users\ViewProviders\LogoutViewProvider $logoutViewProvider,
        private Users\Model\AuthenticationModel $authenticationModel
    ) {
        parent::__construct($context);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(): array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
    {
        if (!$this->user->isAuthenticated()) {
            return $this->redirectResponse->toNewPage($this->appPath->getWebRoot());
        }

        $this->authenticationModel->logout();

        return ($this->logoutViewProvider)();
    }
}
