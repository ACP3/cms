<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\News;

use ACP3\Core\Modules\ModuleRegistration as CoreModuleRegistration;
use ACP3\Core\Router\RoutePathPatterns;
use ACP3\Modules\ACP3\News\Repository\NewsRepository;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ModuleRegistration extends CoreModuleRegistration
{
    public function build(ContainerBuilder $containerBuilder): void
    {
        $definition = $containerBuilder->findDefinition(RoutePathPatterns::class);
        $definition->addMethodCall('addRoutePathPattern', [NewsRepository::TABLE_NAME, Helpers::URL_KEY_PATTERN]);
    }
}
