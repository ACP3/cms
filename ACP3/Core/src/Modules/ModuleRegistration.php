<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Modules;

use Symfony\Component\DependencyInjection\ContainerBuilder;

class ModuleRegistration
{
    /**
     * Allows modifying/extending the dependency injection container.
     */
    public function build(ContainerBuilder $containerBuilder): void
    {
    }
}
