<?php
namespace ACP3\Core;

use ACP3\Modules\System;

/**
 * Manages the various module settings
 * @package ACP3\Core
 */
class Config
{
    /**
     * @var System\Model
     */
    protected $systemModel;
    /**
     * @var Cache
     */
    protected $moduleCache;
    /**
     * @var string
     */
    protected $module = '';
    /**
     * @var array
     */
    protected $settings = [];

    /**
     * @param Cache $moduleCache
     * @param System\Model $systemModel
     * @param $module
     */
    public function __construct(
        Cache $moduleCache,
        System\Model $systemModel,
        $module
    ) {
        $this->moduleCache = $moduleCache;
        $this->systemModel = $systemModel;
        $this->module = strtolower($module);
    }

    /**
     * Erstellt/Verändert die Konfigurationsdateien für die Module
     *
     * @param array $data
     *
     * @return boolean
     */
    public function setSettings($data)
    {
        $bool = $bool2 = false;
        $moduleId = $this->systemModel->getModuleId($this->module);
        if (!empty($moduleId)) {
            foreach ($data as $key => $value) {
                $updateValues = [
                    'value' => $value
                ];
                $where = [
                    'module_id' => $moduleId,
                    'name' => $key
                ];
                $bool = $this->systemModel->update($updateValues, $where, System\Model::TABLE_NAME_SETTINGS);
            }
            $bool2 = $this->setCache();
        }

        return $bool !== false && $bool2 !== false;
    }

    /**
     * Setzt den Cache für die Einstellungen eines Moduls
     *
     * @return boolean
     */
    protected function setCache()
    {
        $settings = $this->systemModel->getSettingsByModuleName($this->module);
        $c_settings = count($settings);

        $data = [];
        for ($i = 0; $i < $c_settings; ++$i) {
            if (is_int($settings[$i]['value'])) {
                $data[$settings[$i]['name']] = (int)$settings[$i]['value'];
            } elseif (is_float($settings[$i]['value'])) {
                $data[$settings[$i]['name']] = (float)$settings[$i]['value'];
            } else {
                $data[$settings[$i]['name']] = $settings[$i]['value'];
            }
        }

        return $this->moduleCache->save('settings', $data);
    }

    /**
     * Gibt den Inhalt der Konfigurationsdateien der Module aus
     *
     * @return array
     */
    public function getSettings()
    {
        if ($this->settings === []) {
            if ($this->moduleCache->contains('settings') === false) {
                $this->setCache();
            }

            $this->settings = $this->moduleCache->fetch('settings');
        }

        return $this->settings;
    }
}
