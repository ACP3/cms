<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Frontend\Account;

use ACP3\Core;
use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Modules\ACP3\Users;

class Settings extends AbstractAction
{
    /**
     * @var \ACP3\Modules\ACP3\Users\ViewProviders\AccountSettingsViewProvider
     */
    private $accountSettingsViewProvider;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        UserModelInterface $user,
        Users\ViewProviders\AccountSettingsViewProvider $accountSettingsViewProvider
    ) {
        parent::__construct($context, $user);

        $this->accountSettingsViewProvider = $accountSettingsViewProvider;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke(): array
    {
        return ($this->accountSettingsViewProvider)();
    }
}
