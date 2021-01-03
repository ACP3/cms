<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Authentication;

use Psr\Container\ContainerInterface;

class AuthenticationFactory
{
    /**
     * @var \Psr\Container\ContainerInterface
     */
    private $authenticationLocator;

    public function __construct(ContainerInterface $authenticationLocator)
    {
        $this->authenticationLocator = $authenticationLocator;
    }

    /**
     * @param string $authenticationMethod
     *
     * @return \ACP3\Core\Authentication\AuthenticationInterface
     *
     * @throws \ACP3\Core\Authentication\Exception\InvalidAuthenticationMethodException
     */
    public function get($authenticationMethod)
    {
        $serviceId = 'core.authentication.' . $authenticationMethod;

        return $this->authenticationLocator->get($serviceId);
    }
}
