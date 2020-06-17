<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Widget\Index;

use ACP3\Core;
use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Modules\ACP3\System\Installer\Schema;
use ACP3\Modules\ACP3\Users\ViewProviders\UserMenuViewProvider;

class UserMenu extends Core\Controller\AbstractWidgetAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var \ACP3\Modules\ACP3\Users\ViewProviders\UserMenuViewProvider
     */
    private $userMenuViewProvider;
    /**
     * @var \ACP3\Core\Authentication\Model\UserModelInterface
     */
    private $user;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        UserModelInterface $user,
        UserMenuViewProvider $userMenuViewProvider
    ) {
        parent::__construct($context);

        $this->userMenuViewProvider = $userMenuViewProvider;
        $this->user = $user;
    }

    /**
     * Displays the user menu, if the user is logged in.
     */
    public function execute(): array
    {
        $this->setCacheResponseCacheable($this->config->getSettings(Schema::MODULE_NAME)['cache_lifetime']);

        if ($this->user->isAuthenticated() === true) {
            return ($this->userMenuViewProvider)();
        }

        $this->setContent(false);

        return [];
    }
}
