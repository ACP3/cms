<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Captcha;

use ACP3\Modules\ACP3\Captcha\DependencyInjection\CaptchaCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ModuleRegistration extends \ACP3\Core\Modules\ModuleRegistration
{
    /**
     * @inheritdoc
     */
    public function build(ContainerBuilder $containerBuilder)
    {
        $containerBuilder->addCompilerPass(new CaptchaCompilerPass());
    }
}
