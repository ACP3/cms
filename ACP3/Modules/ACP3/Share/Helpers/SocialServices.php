<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Helpers;

use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Share\Installer\Schema;

class SocialServices
{
    private static $availableServices = [
        'twitter',
        'facebook',
        'googleplus',
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

    private static $availableBackendServices = [
        'addthis' => 'AddThis',
        'facebook' => 'Facebook',
        'flattr' => 'Flattr',
        'linkedin' => 'LinkedIn',
        'pinterest' => 'Pinterest',
        'reddit' => 'Reddit',
        'stumbleupon' => 'StumbleUpon',
        'vk' => 'Vk',
        'xing' => 'Xing',
    ];

    /**
     * @var \ACP3\Core\Settings\SettingsInterface
     */
    private $settings;

    /**
     * AvailableServices constructor.
     *
     * @param \ACP3\Core\Settings\SettingsInterface $settings
     */
    public function __construct(SettingsInterface $settings)
    {
        $this->settings = $settings;
    }

    public function getAvailableServices(): array
    {
        return static::$availableServices;
    }

    public function getActiveServices(): array
    {
        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

        $activeServices = \unserialize($settings['services']);

        return \array_filter($activeServices, function ($item) {
            return \in_array($item, $this->getAvailableServices());
        });
    }

    public function getAvailableBackendServices(): array
    {
        return static::$availableBackendServices;
    }

    public function getActiveBackendServices(): array
    {
        $intersection = \array_intersect(
            $this->getActiveServices(),
            \array_keys($this->getAvailableBackendServices())
        );

        return \array_values(
            \array_filter(
                $this->getAvailableBackendServices(),
                function ($value, $key) use ($intersection) {
                    return \in_array($key, $intersection);
                },
                \ARRAY_FILTER_USE_BOTH
            )
        );
    }
}
