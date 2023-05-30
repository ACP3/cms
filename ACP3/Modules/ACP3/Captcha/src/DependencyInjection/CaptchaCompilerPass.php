<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Captcha\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class CaptchaCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $captchaLocatorDefinition = $container->findDefinition('captcha.utility.captcha_registrar');

        $locatableCaptchas = [];
        foreach ($container->findTaggedServiceIds('captcha.extension.captcha') as $serviceId => $tags) {
            $locatableCaptchas[$serviceId] = new Reference($serviceId);
        }

        $captchaLocatorDefinition->replaceArgument(0, $locatableCaptchas);
    }
}
