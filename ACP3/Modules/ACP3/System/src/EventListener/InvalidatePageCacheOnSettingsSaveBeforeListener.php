<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\EventListener;

use ACP3\Core\Modules;
use ACP3\Core\Settings\Repository\SettingsAwareRepositoryInterface;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Helper\CanUsePageCache;
use ACP3\Modules\ACP3\System\Installer\Schema;
use ACP3\Modules\ACP3\System\Services\CacheClearService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class InvalidatePageCacheOnSettingsSaveBeforeListener implements EventSubscriberInterface
{
    public function __construct(private readonly SettingsInterface $settings, private readonly Modules $modules, private readonly SettingsAwareRepositoryInterface $settingsRepository, private readonly CanUsePageCache $canUsePageCache, private readonly CacheClearService $cacheClearService)
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
            $systemModuleId = $this->modules->getModuleId(Schema::MODULE_NAME);
            $this->settingsRepository->update(
                ['value' => false],
                ['module_id' => $systemModuleId, 'name' => 'page_cache_is_valid']
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'core.settings.save_before' => '__invoke',
        ];
    }
}
