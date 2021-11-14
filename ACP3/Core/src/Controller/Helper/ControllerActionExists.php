<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Controller\Helper;

use Psr\Container\ContainerInterface;

class ControllerActionExists
{
    public function __construct(protected ContainerInterface $container)
    {
    }

    /**
     * Returns, whether the given module controller action exists.
     */
    public function controllerActionExists(string $path): bool
    {
        $pathArray = explode('/', strtolower($path));

        if (empty($pathArray[2]) === true) {
            $pathArray[2] = 'index';
        }
        if (empty($pathArray[3]) === true) {
            $pathArray[3] = 'index';
        }

        $serviceId = $pathArray[1] . '.controller.' . $pathArray[0] . '.' . $pathArray[2] . '.' . $pathArray[3];

        if ($this->container->has($serviceId)) {
            return true;
        }

        if (!str_ends_with($pathArray[3], '_post')) {
            return $this->container->has($serviceId . '_post');
        }

        return false;
    }
}
