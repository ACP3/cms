<?php
namespace ACP3\Core;

/**
 * Manages the various module settings
 *
 * @author Tino Goratsch
 */
class Config
{

    /**
     * Gibt die Systemeinstellungen aus
     */
    public static function getSystemSettings()
    {
        $settings = self::getSettings('system');
        foreach ($settings as $key => $value) {
            define('CONFIG_' . strtoupper($key), $value);
        }
        return;
    }

    /**
     * Erstellt/Verändert die Konfigurationsdateien für die Module
     *
     * @param string $module
     * @param array $data
     * @return boolean
     */
    public static function setSettings($module, $data)
    {
        $bool = $bool2 = false;
        $mod_id = Registry::get('Db')->fetchColumn('SELECT id FROM ' . DB_PRE . 'modules WHERE name = ?', array($module));
        if (!empty($mod_id)) {
            foreach ($data as $key => $value) {
                $bool = Registry::get('Db')->executeUpdate('UPDATE ' . DB_PRE . 'settings SET value = ? WHERE module_id = ? AND name = ?', array($value, (int)$mod_id, $key));
            }
            $bool2 = self::setModuleCache($module);
        }

        return $bool !== false && $bool2 !== false ? true : false;
    }

    /**
     * Gibt den Inhalt der Konfigurationsdateien der Module aus
     *
     * @param string $module
     * @return array
     */
    public static function getSettings($module)
    {
        if (Cache::check($module, 'settings') === false) {
            self::setModuleCache($module);
        }

        return Cache::output($module, 'settings');
    }

    /**
     * Setzt den Cache für die Einstellungen eines Moduls
     *
     * @param string $module
     * @return boolean
     */
    protected static function setModuleCache($module)
    {
        $settings = Registry::get('Db')->executeQuery('SELECT s.name, s.value FROM ' . DB_PRE . 'settings AS s JOIN ' . DB_PRE . 'modules AS m ON(m.id = s.module_id) WHERE m.name = ?', array($module))->fetchAll();
        $c_settings = count($settings);

        $data = array();
        for ($i = 0; $i < $c_settings; ++$i) {
            if (is_int($settings[$i]['value'])) {
                $data[$settings[$i]['name']] = (int)$settings[$i]['value'];
            } elseif (is_float($settings[$i]['value'])) {
                $data[$settings[$i]['name']] = (float)$settings[$i]['value'];
            } else {
                $data[$settings[$i]['name']] = $settings[$i]['value'];
            }
        }

        return Cache::create($module, $data, 'settings');
    }

}