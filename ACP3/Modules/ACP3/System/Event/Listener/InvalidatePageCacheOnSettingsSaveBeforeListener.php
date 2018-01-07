<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Event\Listener;

use ACP3\Core\Cache\Purge;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Modules\Modules;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Helper\CanUsePageCache;
use ACP3\Modules\ACP3\System\Installer\Schema;
use ACP3\Modules\ACP3\System\Model\Repository\SettingsRepository;

class InvalidatePageCacheOnSettingsSaveBeforeListener
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
     * @var SettingsRepository
     */
    private $settingsRepository;
    /**
     * @var Modules
     */
    private $modules;
    /**
     * @var CanUsePageCache
     */
    private $canUsePageCache;

    /**
     * InvalidatePageCacheOnSettingsSaveBeforeListener constructor.
     *
     * @param ApplicationPath    $applicationPath
     * @param SettingsInterface  $settings
     * @param Modules            $modules
     * @param SettingsRepository $settingsRepository
     * @param CanUsePageCache    $canUsePageCache
     */
    public function __construct(
        ApplicationPath $applicationPath,
        SettingsInterface $settings,
        Modules $modules,
        SettingsRepository $settingsRepository,
        CanUsePageCache $canUsePageCache
    ) {
        $this->applicationPath = $applicationPath;
        $this->settings = $settings;
        $this->modules = $modules;
        $this->settingsRepository = $settingsRepository;
        $this->canUsePageCache = $canUsePageCache;
    }

    public function invalidatePageCache()
    {
        if ($this->canUsePageCache->canUsePageCache()) {
            if ($this->settings->getSettings(Schema::MODULE_NAME)['page_cache_purge_mode'] == 1) {
                Purge::doPurge($this->applicationPath->getCacheDir() . 'http');
            } else {
                $systemModuleId = $this->modules->getModuleId(Schema::MODULE_NAME);
                $this->settingsRepository->update(
                    ['value' => false],
                    ['module_id' => $systemModuleId, 'name' => 'page_cache_is_valid']
                );
            }
        }
    }
}
