<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share;

use ACP3\Modules\ACP3\Share\Shariff\DependencyInjection\RegisterSocialSharingBackendsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ModuleRegistration extends \ACP3\Core\Modules\ModuleRegistration
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(new RegisterSocialSharingBackendsPass());
    }
}
