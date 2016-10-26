<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\System\Event\Listener;

use ACP3\Core\Modules;
use ACP3\Core\Settings\SettingsInterface;
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
     * @var SettingsInterface
     */
    private $settings;

    /**
     * OnModelAfterSaveListener constructor.
     * @param SettingsInterface $settings
     * @param Modules $modules
     * @param SettingsRepository $settingsRepository
     */
    public function __construct(
        SettingsInterface $settings,
        Modules $modules,
        SettingsRepository $settingsRepository
    ) {
        $this->settingsRepository = $settingsRepository;
        $this->modules = $modules;
        $this->settings = $settings;
    }

    public function invalidatePageCache()
    {
        if ($this->settings->getSettings(Schema::MODULE_NAME)['page_cache_is_enabled'] == 1) {
            $systemModuleId = $this->modules->getModuleId(Schema::MODULE_NAME);
            $this->settingsRepository->update(
                ['value' => false],
                ['module_id' => $systemModuleId, 'name' => 'page_cache_is_valid']
            );
        }
    }
}
