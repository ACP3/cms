<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Core\Application\BootstrapCache;

use ACP3\Core\ACL;
use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Installer\Schema;
use FOS\HttpCache\UserContext\ContextProvider;
use FOS\HttpCache\UserContext\UserContext;

class IsAuthenticatedProvider implements ContextProvider
{
    public function __construct(private readonly SettingsInterface $settings, private readonly ACL $acl, private readonly UserModelInterface $userModel)
    {
    }

    /**
     * @param UserContext<int, array<string, mixed>> $context
     */
    public function updateUserContext(UserContext $context): void
    {
        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

        $context->addParameter('security_secret', $settings['security_secret']);
        $context->addParameter('authenticated', $this->userModel->isAuthenticated());
        $context->addParameter('user_id', $this->userModel->getUserId());
        $context->addParameter('roles', $this->acl->getUserRoleIds($this->userModel->getUserId()));
    }
}
