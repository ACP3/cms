<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\System\Event\Listener;


use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Helper\CanUsePageCache;
use ACP3\Modules\ACP3\System\Installer\Schema;

class OnModelAfterSaveListener
{
    /**
     * @var SettingsInterface
     */
    private $settings;
    /**
     * @var CanUsePageCache
     */
    private $canUsePageCache;

    /**
     * OnModelAfterSaveListener constructor.
     * @param SettingsInterface $settings
     * @param CanUsePageCache $canUsePageCache
     */
    public function __construct(
        SettingsInterface $settings,
        CanUsePageCache $canUsePageCache)
    {
        $this->settings = $settings;
        $this->canUsePageCache = $canUsePageCache;
    }

    public function invalidatePageCache()
    {
        if ($this->canUsePageCache->canUsePageCache()) {
            $this->settings->saveSettings(['page_cache_is_valid' => false], Schema::MODULE_NAME);
        }
    }
}
