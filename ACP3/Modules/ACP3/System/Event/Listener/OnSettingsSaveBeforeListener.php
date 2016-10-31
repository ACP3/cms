<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\System\Event\Listener;

use ACP3\Core\Modules;
use ACP3\Modules\ACP3\System\Helper\CanUsePageCache;
use ACP3\Modules\ACP3\System\Installer\Schema;
use ACP3\Modules\ACP3\System\Model\Repository\SettingsRepository;

class OnSettingsSaveBeforeListener
{
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
     * OnModelAfterSaveListener constructor.
     * @param Modules $modules
     * @param SettingsRepository $settingsRepository
     * @param CanUsePageCache $canUsePageCache
     */
    public function __construct(
        Modules $modules,
        SettingsRepository $settingsRepository,
        CanUsePageCache $canUsePageCache
    ) {
        $this->settingsRepository = $settingsRepository;
        $this->modules = $modules;
        $this->canUsePageCache = $canUsePageCache;
    }

    public function invalidatePageCache()
    {
        if ($this->canUsePageCache->canUsePageCache()) {
            $systemModuleId = $this->modules->getModuleId(Schema::MODULE_NAME);
            $this->settingsRepository->update(
                ['value' => false],
                ['module_id' => $systemModuleId, 'name' => 'page_cache_is_valid']
            );
        }
    }
}
