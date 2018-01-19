<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Modules\Helper;

use Symfony\Component\DependencyInjection\ContainerInterface;

class ControllerActionExists
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
     * Returns, whether the given module controller action exists.
     *
     * @param string $path
     *
     * @return bool
     */
    public function controllerActionExists($path)
    {
        $pathArray = \explode('/', \strtolower($path));

        if (empty($pathArray[2]) === true) {
            $pathArray[2] = 'index';
        }
        if (empty($pathArray[3]) === true) {
            $pathArray[3] = 'index';
        }

        $serviceId = $pathArray[1] . '.controller.' . $pathArray[0] . '.' . $pathArray[2] . '.' . $pathArray[3];

        return $this->container->has($serviceId) && \method_exists($this->container->get($serviceId), 'execute');
    }
}
