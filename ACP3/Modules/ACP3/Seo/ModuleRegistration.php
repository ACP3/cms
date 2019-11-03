<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo;

use ACP3\Modules\ACP3\Seo\DependencyInjection\SitemapAvailabilityCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ModuleRegistration extends \ACP3\Core\Modules\ModuleRegistration
{
    public function build(ContainerBuilder $containerBuilder)
    {
        $containerBuilder->addCompilerPass(new SitemapAvailabilityCompilerPass());
    }
}
