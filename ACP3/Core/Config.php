<?php
namespace ACP3\Core;

use ACP3\Modules\ACP3\System;

/**
 * Manages the various module settings
 * @package ACP3\Core
 */
class Config
{
    const CACHE_ID = 'settings';

    /**
     * @var \ACP3\Modules\ACP3\System\Model\Repository\ModuleRepository
     */
    protected $systemModuleRepository;
    /**
     * @var \ACP3\Modules\ACP3\System\Model\Repository\SettingsRepository
     */
    protected $systemSettingsRepository;
    /**
     * @var \ACP3\Core\Cache
     */
    protected $coreCache;
    /**
     * @var array
     */
    protected $settings = [];

    /**
     * @param \ACP3\Core\Cache $coreCache
     * @param \ACP3\Modules\ACP3\System\Model\Repository\ModuleRepository $systemModuleRepository
     * @param \ACP3\Modules\ACP3\System\Model\Repository\SettingsRepository $systemSettingsRepository
     */
    public function __construct(
        Cache $coreCache,
        System\Model\Repository\ModuleRepository $systemModuleRepository,
        System\Model\Repository\SettingsRepository $systemSettingsRepository
    ) {
        $this->coreCache = $coreCache;
        $this->systemModuleRepository = $systemModuleRepository;
        $this->systemSettingsRepository = $systemSettingsRepository;
    }

    /**
     * Saves the module's settings to the database
     *
     * @param array $data
     * @param string $module
     *
     * @return bool
     */
    public function setSettings($data, $module)
    {
        $bool = $bool2 = false;
        $moduleId = $this->systemModuleRepository->getModuleId($module);
        if (!empty($moduleId)) {
            foreach ($data as $key => $value) {
                $updateValues = [
                    'value' => $value
                ];
                $where = [
                    'module_id' => $moduleId,
                    'name' => $key
                ];
                $bool = $this->systemSettingsRepository->update($updateValues, $where);
            }
            $bool2 = $this->saveCache();
        }

        return $bool !== false && $bool2 !== false;
    }

    /**
     * Saves the modules settings to the cache
     *
     * @return bool
     */
    protected function saveCache()
    {
        $settings = $this->systemSettingsRepository->getAllModuleSettings();

        $data = [];
        foreach ($settings as $setting) {
            $data[$setting['module_name']][$setting['name']] = $setting['value'];
        }

        return $this->coreCache->save(static::CACHE_ID, $data);
    }

    /**
     * Returns the module's settings from the cache
     *
     * @param string $module
     * @return array
     */
    public function getSettings($module)
    {
        if ($this->settings === []) {
            if ($this->coreCache->contains(static::CACHE_ID) === false) {
                $this->saveCache();
            }

            $this->settings = $this->coreCache->fetch(static::CACHE_ID);
        }

        return isset($this->settings[$module]) ? $this->settings[$module] : [];
    }
}
