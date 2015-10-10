<?php
namespace ACP3\Core\Modules;

use ACP3\Core\Cache;
use ACP3\Core\Modules\Installer\SchemaInterface;
use Symfony\Component\DependencyInjection\Container;
use ACP3\Modules\ACP3\Permissions;

/**
 * Class AclInstaller
 * @package ACP3\Core\Modules
 */
class AclInstaller implements InstallerInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    protected $container;
    /**
     * @var \ACP3\Core\Cache
     */
    protected $aclCache;
    /**
     * @var \ACP3\Core\Modules\SchemaHelper
     */
    protected $schemaHelper;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Model\RoleRepository
     */
    protected $roleRepository;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Model\PrivilegeRepository
     */
    protected $privilegeRepository;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Model\ResourceRepository
     */
    protected $resourceRepository;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Model\RuleRepository
     */
    protected $ruleRepository;

    /**
     * @param \Symfony\Component\DependencyInjection\Container         $container
     * @param \ACP3\Core\Cache                                         $aclCache
     * @param \ACP3\Core\Modules\SchemaHelper                          $schemaHelper
     * @param \ACP3\Modules\ACP3\Permissions\Model\RoleRepository      $roleRepository
     * @param \ACP3\Modules\ACP3\Permissions\Model\RuleRepository      $ruleRepository
     * @param \ACP3\Modules\ACP3\Permissions\Model\ResourceRepository  $resourceRepository
     * @param \ACP3\Modules\ACP3\Permissions\Model\PrivilegeRepository $privilegeRepository
     */
    public function __construct(
        Container $container,
        Cache $aclCache,
        SchemaHelper $schemaHelper,
        Permissions\Model\RoleRepository $roleRepository,
        Permissions\Model\RuleRepository $ruleRepository,
        Permissions\Model\ResourceRepository $resourceRepository,
        Permissions\Model\PrivilegeRepository $privilegeRepository
    )
    {
        $this->container = $container;
        $this->aclCache = $aclCache;
        $this->schemaHelper = $schemaHelper;
        $this->roleRepository = $roleRepository;
        $this->ruleRepository = $ruleRepository;
        $this->resourceRepository = $resourceRepository;
        $this->privilegeRepository = $privilegeRepository;
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
    public function install(SchemaInterface $schema, $mode = 1)
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
        $moduleId = $this->schemaHelper->getModuleId($module);

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
                $this->resourceRepository->insert($insertValues);
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
        $roles = $this->roleRepository->getAllRoles();
        $privileges = $this->privilegeRepository->getAllPrivilegeIds();
        $moduleId = $this->schemaHelper->getModuleId($moduleName);

        foreach ($roles as $role) {
            foreach ($privileges as $privilege) {
                $insertValues = [
                    'id' => '',
                    'role_id' => $role['id'],
                    'module_id' => $moduleId,
                    'privilege_id' => $privilege['id'],
                    'permission' => $this->getDefaultAclRulePermission($role, $privilege)
                ];
                $this->ruleRepository->insert($insertValues);
            }
        }
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

    /**
     * Löscht die zu einem Modul zugehörigen Ressourcen
     *
     * @param \ACP3\Core\Modules\Installer\SchemaInterface $schema
     *
     * @return bool
     */
    public function uninstall(SchemaInterface $schema)
    {
        $this->aclCache->getDriver()->deleteAll();

        return true;
    }

}