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
    protected $authenticationModel;
    /**
     * @var \ACP3\Core\Router\RouterInterface
     */
    private $router;
    /**
     * @var \ACP3\Core\Http\RedirectResponse
     */
    private $redirectResponse;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Http\RedirectResponse $redirectResponse,
        Core\Router\RouterInterface $router,
        Users\Model\AuthenticationModel $authenticationModel
    ) {
        parent::__construct($context);

        $this->authenticationModel = $authenticationModel;
        $this->router = $router;
        $this->redirectResponse = $redirectResponse;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute()
    {
        if (!$this->user->isAuthenticated()) {
            return $this->redirectResponse->toNewPage($this->appPath->getWebRoot());
        }

        $this->authenticationModel->logout();

        $redirectUrl = $this->appPath->getWebRoot();
        $referer = $this->request->getSymfonyRequest()->headers->get('referer');
        if ($referer !== $this->router->route($this->request->getPathInfo())) {
            $redirectUrl = $referer;
        }

        return [
            'url_homepage' => $this->appPath->getWebRoot(),
            'url_previous_page' => $redirectUrl,
        ];
    }
}
