<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Feeds;


use ACP3\Modules\ACP3\Feeds\DependencyInjection\FeedAvailabilityCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ModuleRegistration extends \ACP3\Modules\ACP3\Search\ModuleRegistration
{
    public function build(ContainerBuilder $containerBuilder)
    {
        $containerBuilder->addCompilerPass(new FeedAvailabilityCompilerPass());
    }
}
