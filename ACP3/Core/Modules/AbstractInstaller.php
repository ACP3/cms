<?php

namespace ACP3\Core\Modules;

use ACP3\Core;

/**
 * Module Installer Klasse
 *
 * @author Tino Goratsch
 */
abstract class AbstractInstaller implements InstallerInterface
{

    /**
     * Die bei der Installation an das Modul zugewiesene ID
     *
     * @var integer
     */
    protected $moduleId = null;

    /**
     * Name des Moduls
     *
     * @var string
     */
    const MODULE_NAME = '';

    /**
     * Version des Tabellen-Schema für das Modul
     * @var integer
     */
    const SCHEMA_VERSION = 0;

    /**
     * Ressourcen, welche vom standardmäßigen Namensschema abweichen
     * oder spezielle Berechtigungen benötigen
     *
     * @var array
     */
    protected $specialResources = array();

    public static function buildClassName($module)
    {
        $moduleName = preg_replace('/(\s+)/', '', ucwords(strtolower(str_replace('_', ' ', $module))));
        return "\\ACP3\\Modules\\$moduleName\\Installer";
    }

    /**
     * Setzt die ID eines Moduls
     */
    public function setModuleId()
    {
        $moduleId = Core\Registry::get('Db')->fetchColumn('SELECT id FROM ' . DB_PRE . 'modules WHERE name = ?', array(static::MODULE_NAME));
        $this->moduleId = !empty($moduleId) ? (int)$moduleId : 0;
    }

    /**
     * Gibt die ID eines Moduls zurück
     *
     * @return integer
     */
    public function getModuleId()
    {
        if (is_null($this->moduleId)) {
            $this->setModuleId();
        }

        return (int)$this->moduleId;
    }

    /**
     * Methode zum Installieren des Moduls
     *
     * @return boolean
     */
    public function install()
    {
        $bool1 = self::executeSqlQueries($this->createTables());
        $bool2 = $this->addToModulesTable();
        $bool3 = $this->installSettings($this->settings());
        $bool4 = $this->addResources();

        return $bool1 && $bool2 && $bool3 && $bool4;
    }

    /**
     * Methode zum Deinstallieren des Moduls
     *
     * @return boolean
     */
    public function uninstall()
    {
        $bool1 = self::executeSqlQueries($this->removeTables());
        $bool2 = $this->removeFromModulesTable();
        $bool3 = $this->removeSettings();
        $bool4 = $this->removeResources();

        return $bool1 && $bool2 && $bool3 && $bool4;
    }

    /**
     * Führt die in $queries als Array übergebenen SQL-Statements aus
     *
     * @param array $queries
     * @return boolean
     */
    public static function executeSqlQueries(array $queries)
    {
        if (count($queries) > 0) {
            $db = Core\Registry::get('Db');
            $search = array('{pre}', '{engine}', '{charset}');
            $replace = array(DB_PRE, 'ENGINE=MyISAM', 'CHARACTER SET `utf8` COLLATE `utf8_general_ci`');

            $db->beginTransaction();
            try {
                foreach ($queries as $query) {
                    if (!empty($query)) {
                        $db->query(str_replace($search, $replace, $query));
                    }
                }
                $db->commit();
            } catch (\Exception $e) {
                $db->rollBack();

                Core\Logger::log('installer', 'warning', $e);
                return false;
            }
        }
        return true;
    }

    /**
     * Gibt ein Array mit den Abhängigkeiten zu anderen Modulen eines Moduls zurück
     *
     * @param string $module
     * @return array
     */
    public static function getDependencies($module)
    {
        if ((bool)preg_match('=/=', $module) === false) {
            $path = MODULES_DIR . $module . '/module.xml';
            if (is_file($path)) {
                $deps = Core\XML::parseXmlFile($path, '/module/info/dependencies');
                return array_values($deps);
            }
        }
    }

    /**
     * Fügt die zu einen Modul zugehörigen Ressourcen ein
     *
     * @param integer $mode
     *    1 = Ressourcen und Regeln einlesen
     *    2 = Nur die Ressourcen einlesen
     * @return boolean
     */
    public function addResources($mode = 1)
    {
        $moduleName = static::MODULE_NAME;
        $dir = ucfirst($moduleName);
        $path = MODULES_DIR . $dir . '/Controller/';
        $files = array("Admin\\Index", 'Index');

        foreach ($files as $file) {
            if (is_file($path . $file . '.php') === true) {
                $className = "\\ACP3\\Modules\\$dir\\Controller\\$file";
                $actions = get_class_methods($className);

                foreach ($actions as $action) {
                    // Nur die Module-Actions als Ressourcen hinzufügen
                    if (strpos($action, 'action') === 0) {
                        $actionUnderscored = strtolower(preg_replace('/\B([A-Z])/', '_$1', $action));
                        // Modulaktionen berücksichtigen, die mit Ziffern anfangen
                        $action = substr($actionUnderscored, strpos($actionUnderscored, '_') === 6 ? 7 : 6);

                        if (isset($this->specialResources[$action])) {
                            $privilegeId = $this->specialResources[$action];
                        } else {
                            if ($file === 'Index') { // Frontend Seiten
                                $privilegeId = 1;
                                if (strpos($action, 'create') === 0) {
                                    $privilegeId = 2;
                                }
                            } else { // Admin-Panel Seiten
                                $action = 'acp_' . $action;
                                $privilegeId = 3;
                                if (strpos($action, 'acp_create') === 0 || strpos($action, 'acp_order') === 0) {
                                    $privilegeId = 4;
                                } elseif (strpos($action, 'acp_edit') === 0) {
                                    $privilegeId = 5;
                                } elseif (strpos($action, 'acp_delete') === 0) {
                                    $privilegeId = 6;
                                } elseif (strpos($action, 'acp_settings') === 0) {
                                    $privilegeId = 7;
                                }
                            }
                        }

                        $insertValues = array(
                            'id' => '',
                            'module_id' => $this->getModuleId(),
                            'page' => $action,
                            'params' => '',
                            'privilege_id' => (int)$privilegeId
                        );
                        Core\Registry::get('Db')->insert(DB_PRE . 'acl_resources', $insertValues);
                    }
                }
            }
        }

        // Regeln für die Rollen setzen
        if ($mode === 1) {
            $roles = Core\Registry::get('Db')->fetchAll('SELECT id FROM ' . DB_PRE . 'acl_roles');
            $privileges = Core\Registry::get('Db')->fetchAll('SELECT id FROM ' . DB_PRE . 'acl_privileges');
            foreach ($roles as $role) {
                foreach ($privileges as $privilege) {
                    $permission = 0;
                    if ($role['id'] == 1 && ($privilege['id'] == 1 || $privilege['id'] == 2)) {
                        $permission = 1;
                    }
                    if ($role['id'] > 1 && $role['id'] < 4) {
                        $permission = 2;
                    }
                    if ($role['id'] == 3 && $privilege['id'] == 3) {
                        $permission = 1;
                    }
                    if ($role['id'] == 4) {
                        $permission = 1;
                    }

                    $insertValues = array(
                        'id' => '',
                        'role_id' => $role['id'],
                        'module_id' => $this->getModuleId(),
                        'privilege_id' => $privilege['id'],
                        'permission' => $permission
                    );
                    Core\Registry::get('Db')->insert(DB_PRE . 'acl_rules', $insertValues);
                }
            }
        }

        Core\Cache::purge(0, 'acl');

        return true;
    }

    /**
     * Löscht die zu einem Modul zugehörigen Ressourcen
     *
     * @return boolean
     */
    protected function removeResources()
    {
        $bool = Core\Registry::get('Db')->delete(DB_PRE . 'acl_resources', array('module_id' => $this->getModuleId()));
        $bool2 = Core\Registry::get('Db')->delete(DB_PRE . 'acl_rules', array('module_id' => $this->getModuleId()));

        Core\Cache::purge(0, 'acl');

        return $bool !== false && $bool2 !== false;
    }

    /**
     * Installiert die zu einem Module zugehörigen Einstellungen
     *
     * @param array $settings
     * @return boolean
     */
    protected function installSettings(array $settings)
    {
        if (count($settings) > 0) {
            $db = Core\Registry::get('Db');
            $db->beginTransaction();
            try {
                foreach ($settings as $key => $value) {
                    $db->insert(DB_PRE . 'settings', array('id' => '', 'module_id' => $this->getModuleId(), 'name' => $key, 'value' => $value));
                }
                $db->commit();
            } catch (\Exception $e) {
                $db->rollback();

                Core\Logger::log('installer', 'warning', $e);
                return false;
            }
        }
        return true;
    }

    /**
     * Löscht die zu einem Module zugehörigen Einstellungen
     *
     * @return boolean
     */
    protected function removeSettings()
    {
        return Core\Registry::get('Db')->delete(DB_PRE . 'settings', array('module_id' => (int)$this->getModuleId())) !== false;
    }

    /**
     * Fügt ein Modul zur modules DB-Tabelle hinzu
     *
     * @return boolean
     */
    protected function addToModulesTable()
    {
        // Modul in die Modules-SQL-Tabelle eintragen
        $insertValues = array(
            'id' => '',
            'name' => static::MODULE_NAME,
            'version' => static::SCHEMA_VERSION,
            'active' => 1
        );
        $bool = Core\Registry::get('Db')->insert(DB_PRE . 'modules', $insertValues);
        $this->moduleId = Core\Registry::get('Db')->lastInsertId();

        return $bool !== false;
    }

    /**
     * Löscht ein Modul aus der modules DB-Tabelle
     * @return boolean
     */
    protected function removeFromModulesTable()
    {
        return Core\Registry::get('Db')->delete(DB_PRE . 'modules', array('id' => (int)$this->getModuleId())) !== false;
    }

    /**
     * Führt die in der Methode schemaUpdates() enthaltenen Tabellenänderungen aus
     *
     * @return integer
     */
    public function updateSchema()
    {
        $module = Core\Registry::get('Db')->fetchAssoc('SELECT version FROM ' . DB_PRE . 'modules WHERE name = ?', array(static::MODULE_NAME));
        $installed_schema_version = isset($module['version']) ? (int)$module['version'] : 0;
        $result = -1;

        // Falls eine Methode zum Umbenennen des Moduls existiert,
        // diese mit der aktuell installierten Schemaverion aufrufen
        $module_names = $this->renameModule();
        if (count($module_names) > 0) {
            $result = $this->interateOverSchemaUpdates($module_names, $installed_schema_version);
            // Modul-ID explizit nochmal neu setzen
            $this->setModuleId();
        }

        $queries = $this->schemaUpdates();
        if (count($queries) > 0) {
            // Nur für den Fall der Fälle... ;)
            ksort($queries);

            $result = $this->interateOverSchemaUpdates($queries, $installed_schema_version);
        }
        return $result;
    }

    /**
     *
     * @param array $schemaUpdates
     * @param integer $installedSchemaVersion
     * @return integer
     */
    protected function interateOverSchemaUpdates(array $schemaUpdates, $installedSchemaVersion)
    {
        $result = -1;
        foreach ($schemaUpdates as $newSchemaVersion => $queries) {
            // Schema-Änderungen nur für neuere Versionen durchführen
            if ($installedSchemaVersion < $newSchemaVersion) {
                // Einzelne Schema-Änderung bei einer Version
                if (!empty($queries) && is_array($queries) === false) {
                    $result = self::executeSqlQueries((array)$queries) === true ? 1 : 0;
                    if ($result !== 0) {
                        $this->setNewSchemaVersion($newSchemaVersion);
                    }
                } else { // Mehrere Schema-Änderungen bei einer Version
                    if (!empty($queries) && is_array($queries) === true) {
                        $result = self::executeSqlQueries($queries) === true ? 1 : 0;
                    }
                    // Falls kein Fehler aufgetreten ist, die Schema Version des Moduls erhöhen
                    if ($result !== 0) {
                        $this->setNewSchemaVersion($newSchemaVersion);
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Setzt die DB-Schema-Version auf die neue Versionsnummer
     *
     * @param integer $newVersion
     * @return boolean
     */
    public function setNewSchemaVersion($newVersion)
    {
        return Core\Registry::get('Db')->update(DB_PRE . 'modules', array('version' => (int)$newVersion), array('name' => static::MODULE_NAME)) !== false;
    }

    /**
     * Methodenstub zum Umbenennen eines Moduls
     *
     * @return array
     */
    public function renameModule()
    {
        return array();
    }
}
