<?php

namespace ACP3\Core\Modules;

/**
 * Module Installer Klasse
 *
 * @author Tino Goratsch
 */
abstract class Installer
{

    /**
     * Die bei der Installation an das Modul zugewiesene ID
     *
     * @var integer
     */
    protected $module_id = null;

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
    protected $special_resources = array();

    public static function buildClassName($module)
    {
        $mod_name = preg_replace('/(\s+)/', '', ucwords(strtolower(str_replace('_', ' ', $module))));
        return "\\ACP3\\Modules\\$mod_name\\Installer";
    }

    /**
     * Setzt die ID eines Moduls
     *
     * @param mixed $module_id
     */
    public function setModuleId()
    {
        $mod_id = \ACP3\Core\Registry::get('Db')->fetchColumn('SELECT id FROM ' . DB_PRE . 'modules WHERE name = ?', array(static::MODULE_NAME));
        $this->module_id = !empty($mod_id) ? (int)$mod_id : 0;
    }

    /**
     * Gibt die ID eines Moduls zurück
     *
     * @return integer
     */
    public function getModuleId()
    {
        if (is_null($this->module_id)) {
            $this->setModuleId();
        }

        return (int)$this->module_id;
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
            $db = \ACP3\Core\Registry::get('Db');
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
                $deps = \ACP3\Core\XML::parseXmlFile($path, '/module/info/dependencies');
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
        $mod_name = static::MODULE_NAME;
        $dir = ucfirst($mod_name);
        $path = MODULES_DIR . $dir . '/Controller/';
        $files = array('Admin', 'Frontend');

        foreach ($files as $file) {
            if (is_file($path . $file . '.php') === true) {
                $className = "\\ACP3\\Modules\\$dir\\Controller\\$file";
                $actions = get_class_methods($className);

                foreach ($actions as $action) {
                    // Nur die Module-Actions als Ressourcen hinzufügen
                    if (strpos($action, 'action') === 0) {
                        $action_underscored = strtolower(preg_replace('/\B([A-Z])/', '_$1', $action));
                        // Modulaktionen berücksichtigen, die mit Ziffern anfangen
                        $action = substr($action_underscored, strpos($action_underscored, '_') === 6 ? 7 : 6);

                        if (isset($this->special_resources[$action])) {
                            $privilege_id = $this->special_resources[$action];
                        } else {
                            if ($file === 'Frontend') { // Frontend Seiten
                                $privilege_id = 1;
                                if (strpos($action, 'create') === 0) {
                                    $privilege_id = 2;
                                }
                            } else { // Admin-Panel Seiten
                                $action = 'acp_' . $action;
                                $privilege_id = 3;
                                if (strpos($action, 'acp_create') === 0 || strpos($action, 'acp_order') === 0) {
                                    $privilege_id = 4;
                                } elseif (strpos($action, 'acp_edit') === 0) {
                                    $privilege_id = 5;
                                } elseif (strpos($action, 'acp_delete') === 0) {
                                    $privilege_id = 6;
                                } elseif (strpos($action, 'acp_settings') === 0) {
                                    $privilege_id = 7;
                                }
                            }
                        }

                        $insert_values = array('id' => '', 'module_id' => $this->getModuleId(), 'page' => $action, 'params' => '', 'privilege_id' => (int)$privilege_id);
                        \ACP3\Core\Registry::get('Db')->insert(DB_PRE . 'acl_resources', $insert_values);
                    }
                }
            }
        }

        if ($mode === 1) {
            // Regeln für die Rollen setzen
            $roles = \ACP3\Core\Registry::get('Db')->fetchAll('SELECT id FROM ' . DB_PRE . 'acl_roles');
            $privileges = \ACP3\Core\Registry::get('Db')->fetchAll('SELECT id FROM ' . DB_PRE . 'acl_privileges');
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

                    $insert_values = array('id' => '', 'role_id' => $role['id'], 'module_id' => $this->getModuleId(), 'privilege_id' => $privilege['id'], 'permission' => $permission);
                    \ACP3\Core\Registry::get('Db')->insert(DB_PRE . 'acl_rules', $insert_values);
                }
            }
        }

        \ACP3\Core\Cache::purge(0, 'acl');

        return true;
    }

    /**
     * Löscht die zu einem Modul zugehörigen Ressourcen
     *
     * @return boolean
     */
    protected function removeResources()
    {
        $bool = \ACP3\Core\Registry::get('Db')->delete(DB_PRE . 'acl_resources', array('module_id' => $this->getModuleId()));
        $bool2 = \ACP3\Core\Registry::get('Db')->delete(DB_PRE . 'acl_rules', array('module_id' => $this->getModuleId()));

        \ACP3\Core\Cache::purge(0, 'acl');

        return $bool && $bool2;
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
            $db = \ACP3\Core\Registry::get('Db');
            $db->beginTransaction();
            try {
                foreach ($settings as $key => $value) {
                    $db->insert(DB_PRE . 'settings', array('id' => '', 'module_id' => $this->getModuleId(), 'name' => $key, 'value' => $value));
                }
                $db->commit();
            } catch (\Exception $e) {
                $db->rollback();
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
        return \ACP3\Core\Registry::get('Db')->delete(DB_PRE . 'settings', array('module_id' => (int)$this->getModuleId())) >= 0 ? true : false;
    }

    /**
     * Fügt ein Modul zur modules DB-Tabelle hinzu
     *
     * @return boolean
     */
    protected function addToModulesTable()
    {
        // Modul in die Modules-SQL-Tabelle eintragen
        $bool = \ACP3\Core\Registry::get('Db')->insert(DB_PRE . 'modules', array('id' => '', 'name' => static::MODULE_NAME, 'version' => static::SCHEMA_VERSION, 'active' => 1));
        $this->module_id = \ACP3\Core\Registry::get('Db')->lastInsertId();

        return (bool)$bool;
    }

    /**
     * Löscht ein Modul aus der modules DB-Tabelle
     * @return boolean
     */
    protected function removeFromModulesTable()
    {
        return \ACP3\Core\Registry::get('Db')->delete(DB_PRE . 'modules', array('id' => (int)$this->getModuleId())) >= 0 ? true : false;
    }

    /**
     * Führt die in der Methode schemaUpdates() enthaltenen Tabellenänderungen aus
     *
     * @param array $queries
     * @return integer
     */
    public function updateSchema()
    {
        $module = \ACP3\Core\Registry::get('Db')->fetchAssoc('SELECT version FROM ' . DB_PRE . 'modules WHERE name = ?', array(static::MODULE_NAME));
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
     * @param array $schema_updates
     * @param integer $installed_schema_version
     * @return integer
     */
    private function interateOverSchemaUpdates(array $schema_updates, $installed_schema_version)
    {
        $result = -1;
        foreach ($schema_updates as $new_schema_version => $queries) {
            // Schema-Änderungen nur für neuere Versionen durchführen
            if ($installed_schema_version < $new_schema_version) {
                // Einzelne Schema-Änderung bei einer Version
                if (!empty($queries) && is_array($queries) === false) {
                    $result = self::executeSqlQueries((array)$queries) === true ? 1 : 0;
                    if ($result !== 0) {
                        $this->setNewSchemaVersion($new_schema_version);
                    }
                    // Mehrere Schema-Änderungen bei einer Version
                } else {
                    if (!empty($queries) && is_array($queries) === true) {
                        $result = self::executeSqlQueries($queries) === true ? 1 : 0;
                    }
                    // Falls kein Fehler aufgetreten ist, die Schema Version des Moduls erhöhen
                    if ($result !== 0) {
                        $this->setNewSchemaVersion($new_schema_version);
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Setzt die DB-Schema-Version auf die neue Versionsnummer
     *
     * @param integer $new_version
     * @return boolean
     */
    public function setNewSchemaVersion($new_version)
    {
        return \ACP3\Core\Registry::get('Db')->update(DB_PRE . 'modules', array('version' => (int)$new_version), array('name' => static::MODULE_NAME)) >= 0 ? true : false;
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

    /**
     * Liefert ein Array mit den zu erstellenden Datenbanktabellen des Moduls zurück
     */
    abstract protected function createTables();

    /**
     * Liefert ein Array mit den zu löschenden Datenbanktabellen des Moduls zurück
     */
    abstract protected function removeTables();

    /**
     * Liefert ein Array mit den zu erstellenden Moduleinstellungen zurück
     */
    abstract protected function settings();

    /**
     * Aktualisiert die Tabellen und Einstellungen eines Moduls auf eine neue Version
     */
    abstract protected function schemaUpdates();
}
