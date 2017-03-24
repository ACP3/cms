<?php
/**
 * Copyright (c) 2017 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Authentication;


use ACP3\Core\Authentication\Exception\InvalidAuthenticationMethodException;

class AuthenticationRegistrar
{
    /**
     * @var AuthenticationInterface[]
     */
    private $authentications = [];

    /**
     * @param string $serviceId
     * @param AuthenticationInterface $authentication
     * @return $this
     */
    public function set($serviceId, AuthenticationInterface $authentication)
    {
        $this->authentications[$serviceId] = $authentication;

        return $this;
    }

    /**
     * @return AuthenticationInterface[]
     */
    public function all()
    {
        return $this->authentications;
    }

    /**
     * @param string $serviceId
     * @return bool
     */
    public function has($serviceId)
    {
        return isset($this->authentications[$serviceId]);
    }

    /**
     * @param string $serviceId
     * @return AuthenticationInterface
     * @throws InvalidAuthenticationMethodException
     */
    public function get($serviceId)
    {
        if ($this->has($serviceId)) {
            return $this->authentications[$serviceId];
        }

        throw new InvalidAuthenticationMethodException(
            sprintf('The authentication with the service id "%s" could not be found.', $serviceId)
        );
    }
}
