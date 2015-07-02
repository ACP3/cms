<?php

namespace ACP3\Core\Modules;

use ACP3\Core;
use ACP3\Modules\ACP3\System;
use ACP3\Modules\ACP3\Permissions;
use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Class AbstractInstaller
 * @package ACP3\Core\Modules
 */
abstract class AbstractInstaller extends ContainerAware implements InstallerInterface
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
     * @var \ACP3\Core\DB
     */
    protected $db;
    /**
     * @var \ACP3\Core\XML
     */
    protected $xml;
    /**
     * @var \ACP3\Core\Cache
     */
    protected $aclCache;
    /**
     * @var \ACP3\Modules\ACP3\System\Model
     */
    protected $systemModel;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Model
     */
    protected $permissionsModel;
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
    protected $specialResources = [];

    /**
     * @param \ACP3\Core\DB                   $db
     * @param \ACP3\Core\XML                  $xml
     * @param \ACP3\Core\Cache                $aclCache
     * @param \ACP3\Modules\ACP3\System\Model      $systemModel
     * @param \ACP3\Modules\ACP3\Permissions\Model $permissionsModel
     */
    public function __construct(
        Core\DB $db,
        Core\XML $xml,
        Core\Cache $aclCache,
        System\Model $systemModel,
        Permissions\Model $permissionsModel
    )
    {
        $this->db = $db;
        $this->xml = $xml;
        $this->aclCache = $aclCache;
        $this->systemModel = $systemModel;
        $this->permissionsModel = $permissionsModel;
    }

    /**
     * Gibt ein Array mit den Abhängigkeiten zu anderen Modulen eines Moduls zurück
     *
     * @return array
     */
    public function getDependencies()
    {
        if ((bool)preg_match('=/=', static::MODULE_NAME) === false) {
            $path = MODULES_DIR . ucfirst(static::MODULE_NAME) . '/config/module.xml';
            if (is_file($path) === true) {
                $deps = $this->xml->parseXmlFile($path, '/module/info/dependencies');
                return array_values($deps);
            }
        }

        return [];
    }

    /**
     * Installs a module
     *
     * @return boolean
     */
    public function install()
    {
        return
            $this->executeSqlQueries($this->createTables()) &&
            $this->addToModulesTable() &&
            $this->installSettings($this->settings()) &&
            $this->addResources();
    }

    /**
     * Executes all given SQL queries
     *
     * @param array $queries
     *
     * @return boolean
     */
    public function executeSqlQueries(array $queries)
    {
        if (count($queries) > 0) {
            $search = ['{pre}', '{engine}', '{charset}'];
            $replace = [$this->db->getPrefix(), 'ENGINE=MyISAM', 'CHARACTER SET `utf8` COLLATE `utf8_general_ci`'];

            $this->db->getConnection()->beginTransaction();
            try {
                foreach ($queries as $query) {
                    if (is_object($query) && ($query instanceof \Closure)) {
                        if ($query() === false) {
                            return false;
                        }
                    } elseif (!empty($query)) {
                        $this->db->getConnection()->query(str_ireplace($search, $replace, $query));
                    }
                }
                $this->db->getConnection()->commit();
            } catch (\Exception $e) {
                $this->db->getConnection()->rollBack();

                Core\Logger::warning('installer', $e);
                return false;
            }
        }
        return true;
    }

    /**
     * Adds a module to the modules SQL-table
     *
     * @return boolean
     */
    protected function addToModulesTable()
    {
        // Modul in die Modules-SQL-Tabelle eintragen
        $insertValues = [
            'id' => '',
            'name' => static::MODULE_NAME,
            'version' => static::SCHEMA_VERSION,
            'active' => 1
        ];
        $lastId = $this->systemModel->insert($insertValues);
        $this->moduleId = $lastId;

        return $lastId !== false;
    }

    /**
     * Installs all module settings
     *
     * @param array $settings
     *
     * @return boolean
     */
    protected function installSettings(array $settings)
    {
        if (count($settings) > 0) {
            $this->db->getConnection()->beginTransaction();
            try {
                foreach ($settings as $key => $value) {
                    $insertValues = [
                        'id' => '',
                        'module_id' => $this->getModuleId(),
                        'name' => $key,
                        'value' => $value
                    ];
                    $this->systemModel->insert($insertValues, System\Model::TABLE_NAME_SETTINGS);
                }
                $this->db->getConnection()->commit();
            } catch (\Exception $e) {
                $this->db->getConnection()->rollback();

                Core\Logger::warning('installer', $e);
                return false;
            }
        }
        return true;
    }

    /**
     * Returns the module-ID
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
     * Sets the module-ID
     */
    public function setModuleId()
    {
        $moduleId = $this->systemModel->getModuleId(static::MODULE_NAME);
        $this->moduleId = !empty($moduleId) ? (int)$moduleId : 0;
    }

    /**
     * Fügt die zu einen Modul zugehörigen Ressourcen ein
     *
     * @param integer $mode
     *    1 = Ressourcen und Regeln einlesen
     *    2 = Nur die Ressourcen einlesen
     *
     * @return boolean
     */
    public function addResources($mode = 1)
    {
        $serviceIds = $this->container->getServiceIds();

        foreach ($serviceIds as $serviceId) {
            if (strpos($serviceId, static::MODULE_NAME . '.controller.') !== false) {
                list($module,, $area, $controller) = explode('.', $serviceId);
                $this->_insertAclResources($module, $controller, $area);
            }
        }

        // Regeln für die Rollen setzen
        if ($mode === 1) {
            $this->_insertAclRules();
        }

        $this->aclCache->getDriver()->deleteAll();

        return true;
    }

    /**
     * Inserts a new resource into the database
     *
     * @param string $module
     * @param string $controller
     * @param string $area
     */
    protected function _insertAclResources($module, $controller, $area)
    {
        $controllerService = $module . '.controller.' . $area . '.' . $controller;
        $actions = get_class_methods($this->container->get($controllerService));

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

                $insertValues = [
                    'id' => '',
                    'module_id' => $this->getModuleId(),
                    'area' => !empty($area) ? strtolower($area) : 'frontend',
                    'controller' => strtolower($controller),
                    'page' => $action,
                    'params' => '',
                    'privilege_id' => (int)$privilegeId
                ];
                $this->permissionsModel->insert($insertValues, Permissions\Model::TABLE_NAME_RESOURCES);
            }
        }
    }

    /**
     * Insert new acl user rules
     */
    protected function _insertAclRules()
    {
        $roles = $this->permissionsModel->getAllRoles();
        $privileges = $this->permissionsModel->getAllResourceIds();
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

                $insertValues = [
                    'id' => '',
                    'role_id' => $role['id'],
                    'module_id' => $this->getModuleId(),
                    'privilege_id' => $privilege['id'],
                    'permission' => $permission
                ];
                $this->permissionsModel->insert($insertValues, Permissions\Model::TABLE_NAME_RULES);
            }
        }
    }

    /**
     * Metod for uninstalling a module
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
     * Löscht ein Modul aus der modules DB-Tabelle
     * @return boolean
     */
    protected function removeFromModulesTable()
    {
        return $this->systemModel->delete((int)$this->getModuleId()) !== false;
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
     * Löscht die zu einem Modul zugehörigen Ressourcen
     *
     * @return boolean
     */
    protected function removeResources()
    {
        $bool = $this->permissionsModel->delete($this->getModuleId(), 'module_id', Permissions\Model::TABLE_NAME_RESOURCES);
        $bool2 = $this->permissionsModel->delete($this->getModuleId(), 'module_id', Permissions\Model::TABLE_NAME_RULES);

        $this->aclCache->getDriver()->deleteAll();

        return $bool !== false && $bool2 !== false;
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
        if (is_array($queries) && count($queries) > 0) {
            // Nur für den Fall der Fälle... ;)
            ksort($queries);

            $result = $this->interateOverSchemaUpdates($queries, $installedSchemaVersion);
        }
        return $result;
    }

    /**
     * Methodenstub zum Umbenennen eines Moduls
     *
     * @return array
     */
    public function renameModule()
    {
        return [];
    }

    /**
     *
     * @param array   $schemaUpdates
     * @param integer $installedSchemaVersion
     *
     * @return integer
     */
    protected function interateOverSchemaUpdates(array $schemaUpdates, $installedSchemaVersion)
    {
        $result = -1;
        foreach ($schemaUpdates as $newSchemaVersion => $queries) {
            // Do schema updates only, if the current schema version is older then the new one
            if ($installedSchemaVersion < $newSchemaVersion &&
                $newSchemaVersion <= static::SCHEMA_VERSION &&
                !empty($queries)
            ) {
                $result = $this->executeSqlQueries((is_array($queries) === false) ? (array)$queries : $queries) === true ? 1 : 0;

                if ($result !== 0) {
                    $this->setNewSchemaVersion($newSchemaVersion);
                }
            }
        }
        return $result;
    }

    /**
     * Setzt die DB-Schema-Version auf die neue Versionsnummer
     *
     * @param integer $newVersion
     *
     * @return boolean
     */
    public function setNewSchemaVersion($newVersion)
    {
        $updateValues = ['version' => (int)$newVersion];
        return $this->systemModel->update($updateValues, ['name' => static::MODULE_NAME]) !== false;
    }

    /**
     * @param string $moduleName
     *
     * @return boolean
     */
    public function moduleIsInstalled($moduleName)
    {
        return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->db->getPrefix() . System\Model::TABLE_NAME . ' WHERE `name` = ?', [$moduleName]) == 1;
    }
}
