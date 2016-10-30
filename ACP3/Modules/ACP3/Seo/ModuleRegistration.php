<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo;


use ACP3\Modules\ACP3\Seo\DependencyInjection\SitemapAvailabilityCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ModuleRegistration extends \ACP3\Core\Modules\ModuleRegistration
{
    /**
     * @param ContainerBuilder $containerBuilder
     */
    public function build(ContainerBuilder $containerBuilder)
    {
        $containerBuilder->addCompilerPass(new SitemapAvailabilityCompilerPass());
    }
}
