<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
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
        'mail',
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
        'print',
        'info',
    ];

    /**
     * @var \ACP3\Core\Settings\SettingsInterface
     */
    private $settings;

    /**
     * AvailableServices constructor.
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

        $activeServices = unserialize($settings['services']);

        return array_filter($activeServices, function($item) {
            return in_array($item, $this->getAvailableServices());
        });
    }
}
