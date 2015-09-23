<?php
namespace ACP3\Core;

use ACP3\Core\Exceptions\InvalidAuthenticationMethod;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AuthenticationFactory
 * @package ACP3\Core
 */
class AuthenticationFactory
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $authenticationMethod
     *
     * @return \ACP3\Core\Authentication\AuthenticationInterface
     * @throws \ACP3\Core\Exceptions\InvalidAuthenticationMethod
     */
    public function get($authenticationMethod)
    {
        $serviceId = 'core.authentication.' . $authenticationMethod;
        if ($this->container->has($serviceId)) {
            return $this->container->get($serviceId);
        }

        throw new InvalidAuthenticationMethod();
    }
}