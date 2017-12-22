<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Captcha\Extension;

use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Captcha\Installer\Schema;
use ACP3\Modules\ACP3\Captcha\Utility\CaptchaRegistrar;

class CaptchaFactory
{
    /**
     * @var SettingsInterface
     */
    private $settings;
    /**
     * @var CaptchaRegistrar
     */
    private $captchaRegistrar;

    /**
     * CaptchaFactory constructor.
     * @param SettingsInterface $settings
     * @param CaptchaRegistrar $captchaRegistrar
     */
    public function __construct(SettingsInterface $settings, CaptchaRegistrar $captchaRegistrar)
    {
        $this->settings = $settings;
        $this->captchaRegistrar = $captchaRegistrar;
    }

    /**
     * @return CaptchaExtensionInterface
     */
    public function create()
    {
        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

        return isset($settings['captcha']) ? $this->captchaRegistrar->getCaptcha($settings['captcha']) : null;
    }
}
