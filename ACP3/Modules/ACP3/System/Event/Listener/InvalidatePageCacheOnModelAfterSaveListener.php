<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Event\Listener;

use ACP3\Core\Cache\Purge;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Helper\CanUsePageCache;
use ACP3\Modules\ACP3\System\Installer\Schema;

class InvalidatePageCacheOnModelAfterSaveListener
{
    /**
     * @var ApplicationPath
     */
    private $applicationPath;
    /**
     * @var SettingsInterface
     */
    private $settings;
    /**
     * @var CanUsePageCache
     */
    private $canUsePageCache;

    /**
     * InvalidatePageCacheOnModelAfterSaveListener constructor.
     *
     * @param ApplicationPath   $applicationPath
     * @param SettingsInterface $settings
     * @param CanUsePageCache   $canUsePageCache
     */
    public function __construct(
        ApplicationPath $applicationPath,
        SettingsInterface $settings,
        CanUsePageCache $canUsePageCache
    ) {
        $this->applicationPath = $applicationPath;
        $this->settings = $settings;
        $this->canUsePageCache = $canUsePageCache;
    }

    public function invalidatePageCache()
    {
        if ($this->canUsePageCache->canUsePageCache()) {
            if ($this->settings->getSettings(Schema::MODULE_NAME)['page_cache_purge_mode'] == 1) {
                Purge::doPurge($this->applicationPath->getCacheDir() . 'http');
            } else {
                $this->settings->saveSettings(['page_cache_is_valid' => false], Schema::MODULE_NAME);
            }
        }
    }
}
