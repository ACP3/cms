<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\System\Event\Listener;


use ACP3\Core\Modules;
use ACP3\Modules\ACP3\System\Installer\Schema;
use ACP3\Modules\ACP3\System\Model\Repository\SettingsRepository;

class OnModelAfterSaveListener
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
     * OnModelAfterSaveListener constructor.
     * @param Modules $modules
     * @param SettingsRepository $settingsRepository
     */
    public function __construct(
        Modules $modules,
        SettingsRepository $settingsRepository)
    {
        $this->settingsRepository = $settingsRepository;
        $this->modules = $modules;
    }

    public function invalidatePageCache()
    {
        $systemModuleId = $this->modules->getModuleId(Schema::MODULE_NAME);
        $this->settingsRepository->update(
            ['value' => false],
            ['module_id' => $systemModuleId, 'name' => 'page_cache_is_valid']
        );
    }
}
