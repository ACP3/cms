<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Event\Listener;

use ACP3\Core\Assets\LibrariesCache;
use ACP3\Core\Model\Repository\SettingsAwareRepositoryInterface;
use ACP3\Core\Modules;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Helper\CanUsePageCache;
use ACP3\Modules\ACP3\System\Installer\Schema;
use Toflar\Psr6HttpCacheStore\Psr6Store;

class InvalidatePageCacheOnSettingsSaveBeforeListener
{
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
    /**
     * @var \Toflar\Psr6HttpCacheStore\Psr6Store
     */
    private $httpCacheStore;
    /**
     * @var \ACP3\Core\Assets\LibrariesCache
     */
    private $librariesCache;

    public function __construct(
        SettingsInterface $settings,
        Modules $modules,
        SettingsAwareRepositoryInterface $settingsRepository,
        CanUsePageCache $canUsePageCache,
        Psr6Store $httpCacheStore,
        LibrariesCache $librariesCache
    ) {
        $this->settings = $settings;
        $this->modules = $modules;
        $this->settingsRepository = $settingsRepository;
        $this->canUsePageCache = $canUsePageCache;
        $this->httpCacheStore = $httpCacheStore;
        $this->librariesCache = $librariesCache;
    }

    public function __invoke()
    {
        if (!$this->canUsePageCache->canUsePageCache()) {
            return;
        }

        if ($this->settings->getSettings(Schema::MODULE_NAME)['page_cache_purge_mode'] == 1) {
            $this->httpCacheStore->clear();
            $this->librariesCache->deleteAll();
        } else {
            $systemModuleId = $this->modules->getModuleId(Schema::MODULE_NAME);
            $this->settingsRepository->update(
                ['value' => false],
                ['module_id' => $systemModuleId, 'name' => 'page_cache_is_valid']
            );
        }
    }
}
