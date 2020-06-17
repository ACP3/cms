<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Widget\Index;

use ACP3\Core;
use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Core\Controller\Context\WidgetContext;
use ACP3\Modules\ACP3\Users\ViewProviders\LoginViewProvider;

class Login extends Core\Controller\AbstractWidgetAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var \ACP3\Modules\ACP3\Users\ViewProviders\LoginViewProvider
     */
    private $loginViewProvider;
    /**
     * @var \ACP3\Core\Authentication\Model\UserModelInterface
     */
    private $user;

    public function __construct(
        WidgetContext $context,
        UserModelInterface $user,
        LoginViewProvider $loginViewProvider
    ) {
        parent::__construct($context);

        $this->loginViewProvider = $loginViewProvider;
        $this->user = $user;
    }

    /**
     * Displays the login mask, if the user is not already logged in.
     */
    public function execute(): ?array
    {
        $this->setCacheResponseCacheable();

        if ($this->user->isAuthenticated() === false) {
            return ($this->loginViewProvider)();
        }

        $this->setContent(false);

        return null;
    }
}
