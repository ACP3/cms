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
    /**
     * @var SettingsInterface
     */
    private $settings;
    /**
     * @var \ACP3\Core\Settings\Repository\SettingsAwareRepositoryInterface
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
     * @var \ACP3\Modules\ACP3\System\Services\CacheClearService
     */
    private $cacheClearService;

    public function __construct(
        SettingsInterface $settings,
        Modules $modules,
        SettingsAwareRepositoryInterface $settingsRepository,
        CanUsePageCache $canUsePageCache,
        CacheClearService $cacheClearService
    ) {
        $this->settings = $settings;
        $this->modules = $modules;
        $this->settingsRepository = $settingsRepository;
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
