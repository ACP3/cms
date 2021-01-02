<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Event\Listener;

use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Helper\CanUsePageCache;
use ACP3\Modules\ACP3\System\Installer\Schema;
use ACP3\Modules\ACP3\System\Services\CacheClearService;

class InvalidatePageCacheOnModelAfterSaveListener
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
     * @var \ACP3\Modules\ACP3\System\Services\CacheClearService
     */
    private $cacheClearService;

    public function __construct(
        SettingsInterface $settings,
        CanUsePageCache $canUsePageCache,
        CacheClearService $cacheClearService
    ) {
        $this->settings = $settings;
        $this->canUsePageCache = $canUsePageCache;
        $this->cacheClearService = $cacheClearService;
    }

    public function __invoke()
    {
        if (!$this->canUsePageCache->canUsePageCache()) {
            return;
        }

        if ($this->settings->getSettings(Schema::MODULE_NAME)['page_cache_purge_mode'] == 1) {
            $this->cacheClearService->clearCacheByType('page');
        } else {
            $this->settings->saveSettings(['page_cache_is_valid' => false], Schema::MODULE_NAME);
        }
    }
}
