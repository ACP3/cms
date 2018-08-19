<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Filemanager\Helper;

use ACP3\Core\Authentication\AuthenticationInterface;
use ACP3\Modules\ACP3\Users\Model\UserModel;

class KCFinderAuthenticationHelper
{
    /**
     * @var \ACP3\Core\Authentication\AuthenticationInterface
     */
    private $authentication;
    /**
     * @var \ACP3\Modules\ACP3\Users\Model\UserModel
     */
    private $userModel;
    /**
     * @var string
     */
    private $environment;

    public function __construct(
        AuthenticationInterface $authentication,
        UserModel $userModel,
        string $environment
    ) {
        $this->authentication = $authentication;
        $this->userModel = $userModel;
        $this->environment = $environment;
    }

    public function checkAuthorization(): bool
    {
        $this->authentication->authenticate();

        return $this->userModel->isAuthenticated();
    }
}
