<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Captcha\Extension;

use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Captcha\Installer\Schema;
use Psr\Container\ContainerInterface;

class CaptchaFactory
{
    public function __construct(private SettingsInterface $settings, private ContainerInterface $captchaLocator)
    {
    }

    /**
     * @return CaptchaExtensionInterface
     */
    public function create()
    {
        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

        return isset($settings['captcha']) ? $this->captchaLocator->get($settings['captcha']) : null;
    }
}
