<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Authentication;

class AuthenticationFactory
{
    /**
     * @var AuthenticationRegistrar
     */
    private $authenticationRegistrar;

    /**
     * @param AuthenticationRegistrar $authenticationRegistrar
     */
    public function __construct(AuthenticationRegistrar $authenticationRegistrar)
    {
        $this->authenticationRegistrar = $authenticationRegistrar;
    }

    /**
     * @param string $authenticationMethod
     *
     * @return \ACP3\Core\Authentication\AuthenticationInterface
     * @throws \ACP3\Core\Authentication\Exception\InvalidAuthenticationMethodException
     */
    public function get($authenticationMethod)
    {
        $serviceId = 'core.authentication.' . $authenticationMethod;

        return $this->authenticationRegistrar->get($serviceId);
    }
}
