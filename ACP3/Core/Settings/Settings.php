<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Settings;

use ACP3\Core\Cache;
use ACP3\Core\Model\Repository\ModuleAwareRepositoryInterface;
use ACP3\Core\Model\Repository\SettingsAwareRepositoryInterface;
use ACP3\Core\Settings\Event\SettingsSaveEvent;
use ACP3\Modules\ACP3\System;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Manages the various module settings
 * @package ACP3\Core\Settings
 */
class Settings implements SettingsInterface
{
    const CACHE_ID = 'settings';

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;
    /**
     * @var ModuleAwareRepositoryInterface
     */
    protected $systemModuleRepository;
    /**
     * @var SettingsAwareRepositoryInterface
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
     * Settings constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param Cache $coreCache
     * @param ModuleAwareRepositoryInterface $systemModuleRepository
     * @param SettingsAwareRepositoryInterface $systemSettingsRepository
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        Cache $coreCache,
        ModuleAwareRepositoryInterface $systemModuleRepository,
        SettingsAwareRepositoryInterface $systemSettingsRepository
    ) {
        $this->coreCache = $coreCache;
        $this->systemModuleRepository = $systemModuleRepository;
        $this->systemSettingsRepository = $systemSettingsRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Saves the module's settings to the database
     *
     * @param array $data
     * @param string $module
     *
     * @return bool
     */
    public function saveSettings(array $data, $module)
    {
        $bool = $bool2 = false;
        $moduleId = $this->systemModuleRepository->getModuleId($module);
        if (!empty($moduleId)) {
            $this->eventDispatcher->dispatch('core.settings.save_before', new SettingsSaveEvent($module, $data));

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
        $settings = $this->systemSettingsRepository->getAllSettings();

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
