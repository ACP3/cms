<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Frontend\Account;

use ACP3\Core;
use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Modules\ACP3\Users;

class Edit extends AbstractAction
{
    /**
     * @var \ACP3\Modules\ACP3\Users\ViewProviders\AccountEditViewProvider
     */
    private $accountEditViewProvider;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        UserModelInterface $user,
        Users\ViewProviders\AccountEditViewProvider $accountEditViewProvider
    ) {
        parent::__construct($context, $user);

        $this->accountEditViewProvider = $accountEditViewProvider;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(): array
    {
        return ($this->accountEditViewProvider)();
    }
}
