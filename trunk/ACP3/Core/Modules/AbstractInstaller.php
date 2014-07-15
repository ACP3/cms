<?php

namespace ACP3\Core\Modules;

use ACP3\Core;
use ACP3\Modules\System;
use ACP3\Modules\Permissions;

/**
 * Module Installer Klasse
 *
 * @author Tino Goratsch
 */
abstract class AbstractInstaller implements InstallerInterface
{
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
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;
    /**
     * @var \ACP3\Modules\System\Model
     */
    protected $systemModel;
    /**
     * @var \ACP3\Modules\Permissions\Model
     */
    protected $aclModel;
    /**
     * Die bei der Installation an das Modul zugewiesene ID
     *
     * @var integer
     */
    protected $moduleId = null;

    /**
     * Ressourcen, welche vom standardmäßigen Namensschema abweichen
     * oder spezielle Berechtigungen benötigen
     *
     * @var array
     */
    protected $specialResources = array();

    public function __construct(
        \Doctrine\DBAL\Connection $db,
        System\Model $systemModel,
        Permissions\Model $permissionsModel
    )
    {
        $this->db = $db;
        $this->systemModel = $systemModel;
        $this->aclModel = $permissionsModel;
    }

    /**
     * @param $module
     * @return string
     */
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
        $moduleId = $this->systemModel->getModuleId(static::MODULE_NAME);
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
        $bool1 = $this->executeSqlQueries($this->createTables());
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
        $bool1 = $this->executeSqlQueries($this->removeTables());
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
    public function executeSqlQueries(array $queries)
    {
        if (count($queries) > 0) {
            $search = array('{pre}', '{engine}', '{charset}');
            $replace = array(DB_PRE, 'ENGINE=MyISAM', 'CHARACTER SET `utf8` COLLATE `utf8_general_ci`');

            $this->db->beginTransaction();
            try {
                foreach ($queries as $query) {
                    if (!empty($query)) {
                        $this->db->query(str_replace($search, $replace, $query));
                    }
                }
                $this->db->commit();
            } catch (\Exception $e) {
                $this->db->rollBack();

                Core\Logger::warning('installer', $e);
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

        return array();
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
        $controllers = scandir($path);

        foreach ($controllers as $controller) {
            if ($controller !== '.' && $controller !== '..') {
                if (is_file($path . $controller) === true) {
                    $this->_insertAclResources($dir, substr($controller, 0, -4));
                } elseif (is_dir($path . $controller) === true) {
                    $subModuleControllers = scandir($path . $controller);

                    foreach ($subModuleControllers as $subController) {
                        if ($subController !== '.' && $subController !== '..' && is_file($path . $controller . '/' . $subController) === true) {
                            $this->_insertAclResources($dir, substr($subController, 0, -4), $controller);
                        }
                    }
                }
            }
        }

        // Regeln für die Rollen setzen
        if ($mode === 1) {
            $this->_insertAclRules();
        }

        $cache = new Core\Cache2('acl');
        $cache->getDriver()->deleteAll();

        return true;
    }

    /**
     * Inserts
     */
    protected function _insertAclRules()
    {
        $roles = $this->aclModel->getAllRoles();
        $privileges = $this->aclModel->getAllResourceIds();
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
                $this->aclModel->insert($insertValues, Permissions\Model::TABLE_NAME_RULES);
            }
        }
    }

    /**
     * Inserts a new resource into the database
     *
     * @param $module
     * @param $controller
     * @param string $area
     */
    protected function _insertAclResources($module, $controller, $area = '')
    {
        if (!empty($area)) {
            $className = "\\ACP3\\Modules\\$module\\Controller\\$area\\$controller";
        } else {
            $className = "\\ACP3\\Modules\\$module\\Controller\\$controller";
        }
        $actions = get_class_methods($className);

        foreach ($actions as $action) {
            // Only add the actual module actions (methods which begin with "action")
            if (strpos($action, 'action') === 0) {
                $actionUnderscored = strtolower(preg_replace('/\B([A-Z])/', '_$1', $action));
                // Modulaktionen berücksichtigen, die mit Ziffern anfangen (Error pages)
                $action = substr($actionUnderscored, strpos($actionUnderscored, '_') === 6 ? 7 : 6);

                // Handle resources with differing access levels
                if (isset($this->specialResources[$area][$controller][$action])) {
                    $privilegeId = $this->specialResources[$area][$controller][$action];
                } else {
                    // Admin panel pages
                    if ($area === 'Admin') {
                        $privilegeId = 3;
                        if (strpos($action, 'create') === 0 || strpos($action, 'order') === 0) {
                            $privilegeId = 4;
                        } elseif (strpos($action, 'edit') === 0) {
                            $privilegeId = 5;
                        } elseif (strpos($action, 'delete') === 0) {
                            $privilegeId = 6;
                        } elseif (strpos($action, 'settings') === 0) {
                            $privilegeId = 7;
                        }
                    } else { // Frontend pages
                        $privilegeId = 1;
                        if (strpos($action, 'create') === 0) {
                            $privilegeId = 2;
                        }
                    }
                }

                $insertValues = array(
                    'id' => '',
                    'module_id' => $this->getModuleId(),
                    'area' => !empty($area) ? strtolower($area) : 'frontend',
                    'controller' => strtolower($controller),
                    'page' => $action,
                    'params' => '',
                    'privilege_id' => (int)$privilegeId
                );
                $this->aclModel->insert($insertValues, Permissions\Model::TABLE_NAME_RESOURCES);
            }
        }
    }

    /**
     * Löscht die zu einem Modul zugehörigen Ressourcen
     *
     * @return boolean
     */
    protected function removeResources()
    {
        $bool = $this->aclModel->delete($this->getModuleId(), 'module_id', Permissions\Model::TABLE_NAME_RESOURCES);
        $bool2 = $this->aclModel->delete($this->getModuleId(), 'module_id', Permissions\Model::TABLE_NAME_RULES);

        $cache = new Core\Cache2('acl');
        $cache->getDriver()->deleteAll();

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
            $this->db->beginTransaction();
            try {
                foreach ($settings as $key => $value) {
                    $insertValues = array(
                        'id' => '',
                        'module_id' => $this->getModuleId(),
                        'name' => $key,
                        'value' => $value
                    );
                    $this->systemModel->insert($insertValues, System\Model::TABLE_NAME_SETTINGS);
                }
                $this->db->commit();
            } catch (\Exception $e) {
                $this->db->rollback();

                Core\Logger::warning('installer', $e);
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
        return $this->systemModel->delete((int)$this->getModuleId(), 'module_id', System\Model::TABLE_NAME_SETTINGS) !== false;
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
        $lastId = $this->systemModel->insert($insertValues);
        $this->moduleId = $lastId;

        return $lastId !== false;
    }

    /**
     * Löscht ein Modul aus der modules DB-Tabelle
     * @return boolean
     */
    protected function removeFromModulesTable()
    {
        return $this->systemModel->delete((int)$this->getModuleId()) !== false;
    }

    /**
     * Führt die in der Methode schemaUpdates() enthaltenen Tabellenänderungen aus
     *
     * @return integer
     */
    public function updateSchema()
    {
        $module = $this->systemModel->getModuleSchemaVersion(static::MODULE_NAME);
        $installedSchemaVersion = !empty($module) ? (int)$module : 0;
        $result = -1;

        // Falls eine Methode zum Umbenennen des Moduls existiert,
        // diese mit der aktuell installierten Schemaverion aufrufen
        $moduleNames = $this->renameModule();
        if (count($moduleNames) > 0) {
            $result = $this->interateOverSchemaUpdates($moduleNames, $installedSchemaVersion);
            // Modul-ID explizit nochmal neu setzen
            $this->setModuleId();
        }

        $queries = $this->schemaUpdates();
        if (count($queries) > 0) {
            // Nur für den Fall der Fälle... ;)
            ksort($queries);

            $result = $this->interateOverSchemaUpdates($queries, $installedSchemaVersion);
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
            if ($installedSchemaVersion < $newSchemaVersion && $newSchemaVersion <= static::SCHEMA_VERSION) {
                // Einzelne Schema-Änderung bei einer Version
                if (!empty($queries) && is_array($queries) === false) {
                    $result = $this->executeSqlQueries((array)$queries) === true ? 1 : 0;
                    if ($result !== 0) {
                        $this->setNewSchemaVersion($newSchemaVersion);
                    }
                } else { // Mehrere Schema-Änderungen bei einer Version
                    if (!empty($queries) && is_array($queries) === true) {
                        $result = $this->executeSqlQueries($queries) === true ? 1 : 0;
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
        $updateValues = array('version' => (int)$newVersion);
        return $this->systemModel->update($updateValues, array('name' => static::MODULE_NAME)) !== false;
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
