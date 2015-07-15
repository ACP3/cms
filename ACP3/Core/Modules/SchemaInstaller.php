<?php

namespace ACP3\Core\Modules;

use ACP3\Core;
use ACP3\Core\Modules\Installer\SchemaInterface;
use ACP3\Modules\ACP3\System;
use ACP3\Modules\ACP3\Permissions;

/**
 * Class SchemaInstaller
 * @package ACP3\Core\Modules
 */
class SchemaInstaller extends SchemaHelper
{
    /**
     * @var \ACP3\Core\XML
     */
    protected $xml;
    /**
     * @var \ACP3\Core\Cache
     */
    protected $aclCache;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Model
     */
    protected $permissionsModel;

    /**
     * @param \ACP3\Core\DB                        $db
     * @param \ACP3\Core\XML                       $xml
     * @param \ACP3\Core\Cache                     $aclCache
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
        parent::__construct($db, $systemModel);

        $this->xml = $xml;
        $this->aclCache = $aclCache;
        $this->permissionsModel = $permissionsModel;
    }

    /**
     * Gibt ein Array mit den Abhängigkeiten zu anderen Modulen eines Moduls zurück
     *
     * @param string $moduleName
     *
     * @return array
     */
    public function getDependencies($moduleName)
    {
        if ((bool)preg_match('=/=', $moduleName) === false) {
            $path = MODULES_DIR . ucfirst($moduleName) . '/config/module.xml';
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
     * @param \ACP3\Core\Modules\Installer\SchemaInterface $schema
     *
     * @return bool
     */
    public function install(SchemaInterface $schema)
    {
        return
            $this->executeSqlQueries($schema->createTables(), $schema->getModuleName()) &&
            $this->addToModulesTable($schema->getModuleName(), $schema->getSchemaVersion()) &&
            $this->installSettings($schema->getModuleName(), $schema->settings()) &&
            $this->addResources($schema);
    }

    /**
     * Adds a module to the modules SQL-table
     *
     * @param string $moduleName
     * @param int    $schemaVersion
     *
     * @return bool
     */
    protected function addToModulesTable($moduleName, $schemaVersion)
    {
        // Modul in die Modules-SQL-Tabelle eintragen
        $insertValues = [
            'id' => '',
            'name' => $moduleName,
            'version' => $schemaVersion,
            'active' => 1
        ];

        return $this->systemModel->insert($insertValues) !== false;
    }

    /**
     * Installs all module settings
     *
     * @param string $moduleName
     * @param array  $settings
     *
     * @return bool
     * @throws \Doctrine\DBAL\ConnectionException
     */
    protected function installSettings($moduleName, array $settings)
    {
        if (count($settings) > 0) {
            $this->db->getConnection()->beginTransaction();
            try {
                $moduleId = $this->getModuleId($moduleName);
                foreach ($settings as $key => $value) {
                    $insertValues = [
                        'id' => '',
                        'module_id' => $moduleId,
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
     * Fügt die zu einen Modul zugehörigen Ressourcen ein
     *
     * @param \ACP3\Core\Modules\Installer\SchemaInterface $schema
     * @param int                                          $mode
     *    1 = Ressourcen und Regeln einlesen
     *    2 = Nur die Ressourcen einlesen
     *
     * @return bool
     */
    public function addResources(SchemaInterface $schema, $mode = 1)
    {
        $serviceIds = $this->container->getServiceIds();

        foreach ($serviceIds as $serviceId) {
            if (strpos($serviceId, $schema->getModuleName() . '.controller.') !== false) {
                list($module, , $area, $controller) = explode('.', $serviceId);
                $this->_insertAclResources($module, $controller, $area, $schema->specialResources());
            }
        }

        // Regeln für die Rollen setzen
        if ($mode === 1) {
            $this->_insertAclRules($schema->getModuleName());
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
     * @param array  $specialResources
     */
    protected function _insertAclResources($module, $controller, $area, array $specialResources)
    {
        $controllerService = $module . '.controller.' . $area . '.' . $controller;
        $actions = get_class_methods($this->container->get($controllerService));
        $moduleId = $this->getModuleId($module);

        foreach ($actions as $action) {
            // Only add the actual module actions (methods which begin with "action")
            if (strpos($action, 'action') === 0) {
                $actionUnderscored = strtolower(preg_replace('/\B([A-Z])/', '_$1', $action));
                // Modulaktionen berücksichtigen, die mit Ziffern anfangen (Error pages)
                $action = substr($actionUnderscored, strpos($actionUnderscored, '_') === 6 ? 7 : 6);

                // Handle resources with differing access levels
                if (isset($specialResources[$area][$controller][$action])) {
                    $privilegeId = $specialResources[$area][$controller][$action];
                } else {
                    $privilegeId = $this->getDefaultAclPrivilegeId($area, $action);
                }

                $insertValues = [
                    'id' => '',
                    'module_id' => $moduleId,
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
     *
     * @param string $moduleName
     */
    protected function _insertAclRules($moduleName)
    {
        $roles = $this->permissionsModel->getAllRoles();
        $privileges = $this->permissionsModel->getAllResourceIds();
        $moduleId = $this->getModuleId($moduleName);

        foreach ($roles as $role) {
            foreach ($privileges as $privilege) {
                $insertValues = [
                    'id' => '',
                    'role_id' => $role['id'],
                    'module_id' => $moduleId,
                    'privilege_id' => $privilege['id'],
                    'permission' => $this->getDefaultAclRulePermission($role, $privilege)
                ];
                $this->permissionsModel->insert($insertValues, Permissions\Model::TABLE_NAME_RULES);
            }
        }
    }

    /**
     * Method for uninstalling a module
     *
     * @param \ACP3\Core\Modules\Installer\SchemaInterface $schema
     *
     * @return bool
     */
    public function uninstall(SchemaInterface $schema)
    {
        $bool1 = $this->executeSqlQueries($schema->removeTables(), $schema->getModuleName());
        $bool2 = $this->removeFromModulesTable($schema->getModuleName());
        $bool3 = $this->removeSettings($schema->getModuleName());
        $bool4 = $this->removeResources($schema->getModuleName());

        return $bool1 && $bool2 && $bool3 && $bool4;
    }

    /**
     * Löscht ein Modul aus der modules DB-Tabelle
     *
     * @param string $moduleName
     *
     * @return bool
     */
    protected function removeFromModulesTable($moduleName)
    {
        return $this->systemModel->delete((int)$this->getModuleId($moduleName)) !== false;
    }

    /**
     * Löscht die zu einem Module zugehörigen Einstellungen
     *
     * @param string $moduleName
     *
     * @return bool
     */
    protected function removeSettings($moduleName)
    {
        return $this->systemModel->delete((int)$this->getModuleId($moduleName), 'module_id', System\Model::TABLE_NAME_SETTINGS) !== false;
    }

    /**
     * Löscht die zu einem Modul zugehörigen Ressourcen
     *
     * @param string $moduleName
     *
     * @return bool
     */
    protected function removeResources($moduleName)
    {
        $bool = $this->permissionsModel->delete($this->getModuleId($moduleName), 'module_id', Permissions\Model::TABLE_NAME_RESOURCES);
        $bool2 = $this->permissionsModel->delete($this->getModuleId($moduleName), 'module_id', Permissions\Model::TABLE_NAME_RULES);

        $this->aclCache->getDriver()->deleteAll();

        return $bool !== false && $bool2 !== false;
    }

    /**
     * @param string $area
     * @param string $action
     *
     * @return int
     */
    protected function getDefaultAclPrivilegeId($area, $action)
    {
        if ($area === 'Admin') {
            if (strpos($action, 'create') === 0 || strpos($action, 'order') === 0) {
                return 4;
            } elseif (strpos($action, 'edit') === 0) {
                return 5;
            } elseif (strpos($action, 'delete') === 0) {
                return 6;
            } elseif (strpos($action, 'settings') === 0) {
                return 7;
            }

            return 3;
        }

        // Frontend controller actions
        if (strpos($action, 'create') === 0) {
            return 2;
        } else {
            return 1;
        }
    }

    /**
     * @param array $role
     * @param array $privilege
     *
     * @return int
     */
    protected function getDefaultAclRulePermission($role, $privilege)
    {
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

        return $permission;
    }
}
