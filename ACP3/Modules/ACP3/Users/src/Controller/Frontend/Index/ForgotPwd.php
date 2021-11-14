<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Modules\ACP3\Users;

class ForgotPwd extends Core\Controller\AbstractWidgetAction
{
    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        private UserModelInterface $user,
        private Core\Http\RedirectResponse $redirectResponse,
        private Users\ViewProviders\ForgotPasswordViewProvider $forgotPasswordViewProvider
    ) {
        parent::__construct($context);
    }

    public function __invoke(): array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
    {
        if ($this->user->isAuthenticated() === true) {
            return $this->redirectResponse->toNewPage($this->appPath->getWebRoot());
        }

        return ($this->forgotPasswordViewProvider)();
    }
}
