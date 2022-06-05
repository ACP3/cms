<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\EventListener;

use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Helper\CanUsePageCache;
use ACP3\Modules\ACP3\System\Installer\Schema;
use ACP3\Modules\ACP3\System\Services\CacheClearService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class InvalidatePageCacheOnModelAfterSaveListener implements EventSubscriberInterface
{
    public function __construct(private readonly SettingsInterface $settings, private readonly CanUsePageCache $canUsePageCache, private readonly CacheClearService $cacheClearService)
    {
    }

    public function __invoke(): void
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

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'core.model.after_save' => '__invoke',
            'core.model.after_delete' => '__invoke',
        ];
    }
}
