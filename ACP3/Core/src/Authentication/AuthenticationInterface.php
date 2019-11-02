<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Authentication;

use ACP3\Core\Authentication\Model\UserModelInterface;

interface AuthenticationInterface
{
    public function authenticate(UserModelInterface $userModel): void;
}
