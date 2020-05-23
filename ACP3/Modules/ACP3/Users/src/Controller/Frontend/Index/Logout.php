<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Users;

class Logout extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var Users\Model\AuthenticationModel
     */
    private $authenticationModel;
    /**
     * @var \ACP3\Core\Http\RedirectResponse
     */
    private $redirectResponse;
    /**
     * @var \ACP3\Modules\ACP3\Users\ViewProviders\LogoutViewProvider
     */
    private $logoutViewProvider;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Http\RedirectResponse $redirectResponse,
        Users\ViewProviders\LogoutViewProvider $logoutViewProvider,
        Users\Model\AuthenticationModel $authenticationModel
    ) {
        parent::__construct($context);

        $this->authenticationModel = $authenticationModel;
        $this->redirectResponse = $redirectResponse;
        $this->logoutViewProvider = $logoutViewProvider;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute()
    {
        if (!$this->user->isAuthenticated()) {
            return $this->redirectResponse->toNewPage($this->appPath->getWebRoot());
        }

        $this->authenticationModel->logout();

        return ($this->logoutViewProvider)();
    }
}
