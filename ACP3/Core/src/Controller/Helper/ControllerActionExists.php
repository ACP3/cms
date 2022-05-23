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
        if (str_contains($path, '/') === false) {
            return false;
        }

        $defaults = [2 => 'index', 3 => 'index'];
        [$area, $module, $controller, $action] = explode('/', strtolower($path)) + $defaults;

        $serviceId = $module . '.controller.' . $area . '.' . $controller . '.' . $action;

        if ($this->container->has($serviceId)) {
            return true;
        }

        if (!str_ends_with($action, '_post')) {
            return $this->container->has($serviceId . '_post');
        }

        return false;
    }
}
