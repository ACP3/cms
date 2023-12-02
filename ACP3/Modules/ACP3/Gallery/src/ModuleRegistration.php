<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery;

use ACP3\Core\Modules\ModuleRegistration as CoreModuleRegistration;
use ACP3\Core\Router\RoutePathPatterns;
use ACP3\Modules\ACP3\Gallery\Repository\GalleryRepository;
use ACP3\Modules\ACP3\Gallerycomments\Repository\GalleryPictureRepository;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ModuleRegistration extends CoreModuleRegistration
{
    public function build(ContainerBuilder $containerBuilder): void
    {
        $definition = $containerBuilder->findDefinition(RoutePathPatterns::class);
        $definition->addMethodCall('addRoutePathPattern', [GalleryRepository::TABLE_NAME, Helpers::URL_KEY_PATTERN_GALLERY]);
        $definition->addMethodCall('addRoutePathPattern', [GalleryPictureRepository::TABLE_NAME, Helpers::URL_KEY_PATTERN_PICTURE]);
    }
}
