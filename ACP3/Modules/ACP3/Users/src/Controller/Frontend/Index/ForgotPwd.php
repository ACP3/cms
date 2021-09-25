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
    /**
     * @var \ACP3\Core\Http\RedirectResponse
     */
    private $redirectResponse;
    /**
     * @var \ACP3\Modules\ACP3\Users\ViewProviders\ForgotPasswordViewProvider
     */
    private $forgotPasswordViewProvider;
    /**
     * @var \ACP3\Core\Authentication\Model\UserModelInterface
     */
    private $user;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        UserModelInterface $user,
        Core\Http\RedirectResponse $redirectResponse,
        Users\ViewProviders\ForgotPasswordViewProvider $forgotPasswordViewProvider
    ) {
        parent::__construct($context);

        $this->redirectResponse = $redirectResponse;
        $this->forgotPasswordViewProvider = $forgotPasswordViewProvider;
        $this->user = $user;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function __invoke()
    {
        if ($this->user->isAuthenticated() === true) {
            return $this->redirectResponse->toNewPage($this->appPath->getWebRoot());
        }

        return ($this->forgotPasswordViewProvider)();
    }
}
