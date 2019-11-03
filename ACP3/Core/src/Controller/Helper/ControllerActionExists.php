<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Controller\Helper;

use Psr\Container\ContainerInterface;

class ControllerActionExists
{
    /**
     * @var \Psr\Container\ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Returns, whether the given module controller action exists.
     *
     * @return bool
     */
    public function controllerActionExists(string $path)
    {
        $pathArray = \explode('/', \strtolower($path));

        if (empty($pathArray[2]) === true) {
            $pathArray[2] = 'index';
        }
        if (empty($pathArray[3]) === true) {
            $pathArray[3] = 'index';
        }

        $serviceId = $pathArray[1] . '.controller.' . $pathArray[0] . '.' . $pathArray[2] . '.' . $pathArray[3];

        return $this->container->has($serviceId);
    }
}
