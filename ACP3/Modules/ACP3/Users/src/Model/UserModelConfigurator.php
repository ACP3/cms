<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Model;

use ACP3\Core\Authentication\AuthenticationInterface;
use ACP3\Core\Authentication\Model\UserModelInterface;

class UserModelConfigurator
{
    public function __construct(private readonly AuthenticationInterface $authentication)
    {
    }

    public function configure(UserModelInterface $userModel): void
    {
        $this->authentication->authenticate($userModel);
    }
}
