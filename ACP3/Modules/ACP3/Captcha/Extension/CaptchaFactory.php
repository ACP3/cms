<?php
/**
 * Copyright (c) 2017 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Captcha\Extension;


use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Captcha\Installer\Schema;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CaptchaFactory
{
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var SettingsInterface
     */
    private $settings;

    /**
     * CaptchaFactory constructor.
     * @param SettingsInterface $settings
     * @param ContainerInterface $container
     */
    public function __construct(SettingsInterface $settings, ContainerInterface $container)
    {
        $this->container = $container;
        $this->settings = $settings;
    }

    /**
     * @return CaptchaExtensionInterface
     */
    public function create()
    {
        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

        if ($this->container->has($settings['captcha'])) {
            /** @var CaptchaExtensionInterface $service */
            $service = $this->container->get($settings['captcha']);

            return $service;
        }

        throw new \InvalidArgumentException(
            sprintf('Can not find the captcha extension with the name "%s".', $settings['captcha'])
        );
    }
}
