<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Helper;

use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Installer\Schema;

class CanUsePageCache
{
    /**
     * @var SettingsInterface
     */
    private $settings;
    /**
     * @var string
     */
    private $environment;

    /**
     * CanUsePageCache constructor.
     * @param SettingsInterface $settings
     * @param $environment
     */
    public function __construct(SettingsInterface $settings, $environment)
    {
        $this->settings = $settings;
        $this->environment = $environment;
    }

    /**
     * @return bool
     */
    public function canUsePageCache()
    {
        $systemSettings = $this->settings->getSettings(Schema::MODULE_NAME);

        return $systemSettings['page_cache_is_enabled'] == 1 && $this->environment === ApplicationMode::PRODUCTION;
    }
}
