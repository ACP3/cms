<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Event\Listener;

use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Helper\CanUsePageCache;
use ACP3\Modules\ACP3\System\Installer\Schema;
use Toflar\Psr6HttpCacheStore\Psr6Store;

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
     * @var \Toflar\Psr6HttpCacheStore\Psr6Store
     */
    private $httpCacheStore;

    public function __construct(
        SettingsInterface $settings,
        CanUsePageCache $canUsePageCache,
        Psr6Store $httpCacheStore
    ) {
        $this->settings = $settings;
        $this->canUsePageCache = $canUsePageCache;
        $this->httpCacheStore = $httpCacheStore;
    }

    public function __invoke()
    {
        if (!$this->canUsePageCache->canUsePageCache()) {
            return;
        }

        if ($this->settings->getSettings(Schema::MODULE_NAME)['page_cache_purge_mode'] == 1) {
            $this->httpCacheStore->clear();
        } else {
            $this->settings->saveSettings(['page_cache_is_valid' => false], Schema::MODULE_NAME);
        }
    }
}
