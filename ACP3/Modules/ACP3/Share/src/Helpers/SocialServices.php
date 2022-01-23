<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Helpers;

use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Share\Installer\Schema;
use ACP3\Modules\ACP3\Share\Shariff\SocialSharingBackendServiceLocator;

class SocialServices
{
    /**
     * @var string[]
     */
    private static array $servicesMap = [
        'twitter',
        'facebook',
        'linkedin',
        'pinterest',
        'xing',
        'whatsapp',
        'addthis',
        'tumblr',
        'flattr',
        'diaspora',
        'reddit',
        'stumbleupon',
        'threema',
        'weibo',
        'tencent - weibo',
        'qzone',
        'telegram',
        'vk',
        'mail',
        'print',
        'info',
    ];

    public function __construct(private SettingsInterface $settings, private SocialSharingBackendServiceLocator $locator)
    {
    }

    /**
     * @return string[]
     */
    public function getAllServices(): array
    {
        return self::$servicesMap;
    }

    /**
     * @return string[]
     */
    public function getActiveServices(): array
    {
        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

        $activeServices = unserialize($settings['services']);
        if (\is_array($activeServices) === false) {
            $activeServices = [];
        }

        // Although we already know the active social sharing services, we are still doing the array_intersect,
        // so that we don't end up with any invalid services.
        return array_intersect(
            $this->getAllServices(),
            $activeServices
        );
    }

    /**
     * @return string[]
     */
    public function getAllBackendServices(): array
    {
        return array_keys($this->locator->getServices());
    }

    /**
     * @return string[]
     */
    public function getActiveBackendServices(): array
    {
        $activeServices = $this->getActiveServices();

        return array_values(
            array_filter(
                $this->getAllBackendServices(),
                static fn (?string $value) => \in_array($value, $activeServices, true),
            )
        );
    }
}
