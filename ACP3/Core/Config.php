<?php
namespace ACP3\Core;

use ACP3\Modules\ACP3\System;

/**
 * Manages the various module settings
 * @package ACP3\Core
 */
class Config
{
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
     * @param \ACP3\Core\Cache                                   $coreCache
     * @param \ACP3\Modules\ACP3\System\Model\Repository\ModuleRepository   $systemModuleRepository
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
     * Erstellt/Verändert die Konfigurationsdateien für die Module
     *
     * @param array  $data
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
     * Setzt den Cache für die Einstellungen eines Moduls
     *
     * @return bool
     */
    protected function saveCache()
    {
        $settings = $this->systemSettingsRepository->getAllModuleSettings();

        $data = [];
        foreach ($settings as $setting) {
            if (is_int($setting['value'])) {
                $data[$setting['module_name']][$setting['name']] = (int)$setting['value'];
            } elseif (is_float($setting['value'])) {
                $data[$setting['module_name']][$setting['name']] = (float)$setting['value'];
            } else {
                $data[$setting['module_name']][$setting['name']] = $setting['value'];
            }
        }

        return $this->coreCache->save('settings', $data);
    }

    /**
     * Gibt den Inhalt der Konfigurationsdateien der Module aus
     *
     * @param string $module
     *
     * @return array
     */
    public function getSettings($module)
    {
        if ($this->settings === []) {
            if ($this->coreCache->contains('settings') === false) {
                $this->saveCache();
            }

            $this->settings = $this->coreCache->fetch('settings');
        }

        return isset($this->settings[$module]) ? $this->settings[$module] : [];
    }
}
