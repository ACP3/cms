<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Event\Listener;

use ACP3\Core\Cache\Purge;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Model\Repository\SettingsAwareRepositoryInterface;
use ACP3\Core\Modules;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Helper\CanUsePageCache;
use ACP3\Modules\ACP3\System\Installer\Schema;

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
     * @var \ACP3\Core\Model\Repository\SettingsAwareRepositoryInterface
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

    public function __construct(
        ApplicationPath $applicationPath,
        SettingsInterface $settings,
        Modules $modules,
        SettingsAwareRepositoryInterface $settingsRepository,
        CanUsePageCache $canUsePageCache
    ) {
        $this->applicationPath = $applicationPath;
        $this->settings = $settings;
        $this->modules = $modules;
        $this->settingsRepository = $settingsRepository;
        $this->canUsePageCache = $canUsePageCache;
    }

    public function __invoke()
    {
        if (!$this->canUsePageCache->canUsePageCache()) {
            return;
        }

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
