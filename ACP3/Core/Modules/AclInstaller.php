<?php
namespace ACP3\Core\Modules;

use ACP3\Core\ACL\PermissionEnum;
use ACP3\Core\ACL\PrivilegeEnum;
use ACP3\Core\Cache;
use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Modules\Installer\SchemaInterface;
use ACP3\Modules\ACP3\Permissions;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class AclInstaller
 * @package ACP3\Core\Modules
 */
class AclInstaller implements InstallerInterface
{
    const INSTALL_RESOURCES_AND_RULES = 1;
    const INSTALL_RESOURCES = 2;

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
    ) {
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
     *
     * @return bool
     */
    public function install(SchemaInterface $schema, $mode = self::INSTALL_RESOURCES_AND_RULES)
    {
        $serviceIds = $this->container->getServiceIds();

        foreach ($serviceIds as $serviceId) {
            if (strpos($serviceId, $schema->getModuleName() . '.controller.') !== false) {
                $this->insertAclResources($serviceId, $schema->specialResources());
            }
        }

        // Regeln für die Rollen setzen
        if ($mode === self::INSTALL_RESOURCES_AND_RULES) {
            $this->insertAclRules($schema->getModuleName());
        }

        $this->aclCache->getDriver()->deleteAll();

        return true;
    }

    /**
     * Inserts a new resource into the database
     *
     * @param string $serviceId
     * @param array  $specialResources
     */
    protected function insertAclResources($serviceId, array $specialResources)
    {
        list($module, , $area, $controller, $action) = explode('.', $serviceId);

        // Only add the actual module actions (methods which begin with "action")
        if ($area !== AreaEnum::AREA_INSTALL && method_exists($this->container->get($serviceId), 'execute') === true) {
            $action = $this->convertCamelCaseToUnderscore($action);

            // Handle resources with differing access levels
            if (isset($specialResources[$area][$controller][$action])) {
                $privilegeId = $specialResources[$area][$controller][$action];
            } else {
                $privilegeId = $this->getDefaultAclPrivilegeId($area, $action);
            }

            $insertValues = [
                'id' => '',
                'module_id' => $this->schemaHelper->getModuleId($module),
                'area' => !empty($area) ? strtolower($area) : AreaEnum::AREA_FRONTEND,
                'controller' => strtolower($controller),
                'page' => $action,
                'params' => '',
                'privilege_id' => (int)$privilegeId
            ];
            $this->resourceRepository->insert($insertValues);
        }
    }

    /**
     * Insert new acl user rules
     *
     * @param string $moduleName
     */
    protected function insertAclRules($moduleName)
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
        $area = strtolower($area);
        $actionPrivilegeMapping = $this->getActionPrivilegeMapping();

        if (isset($actionPrivilegeMapping[$area])) {
            foreach ($actionPrivilegeMapping[$area] as $actionName => $privilegeId) {
                if (strpos($action, $actionName) === 0) {
                    return $privilegeId;
                }
            }
        }

        if ($area === AreaEnum::AREA_ADMIN) {
            return PrivilegeEnum::ADMIN_VIEW;
        }

        return PrivilegeEnum::FRONTEND_VIEW;
    }

    /**
     * @return array
     */
    protected function getActionPrivilegeMapping()
    {
        return [
            AreaEnum::AREA_ADMIN => [
                'create' => PrivilegeEnum::ADMIN_CREATE,
                'order' => PrivilegeEnum::ADMIN_CREATE,
                'edit' => PrivilegeEnum::ADMIN_EDIT,
                'delete' => PrivilegeEnum::ADMIN_DELETE,
                'settings' => PrivilegeEnum::ADMIN_SETTINGS
            ],
            AreaEnum::AREA_FRONTEND => [
                'create' => PrivilegeEnum::FRONTEND_CREATE
            ]
        ];
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
        if ($role['id'] == 1 &&
            ($privilege['id'] == PrivilegeEnum::FRONTEND_VIEW || $privilege['id'] == PrivilegeEnum::FRONTEND_CREATE)
        ) {
            $permission = PermissionEnum::PERMIT_ACCESS;
        }
        if ($role['id'] > 1 && $role['id'] < 4) {
            $permission = PermissionEnum::INHERIT_ACCESS;
        }
        if ($role['id'] == 3 && $privilege['id'] == PrivilegeEnum::ADMIN_VIEW) {
            $permission = PermissionEnum::PERMIT_ACCESS;
        }
        if ($role['id'] == 4) {
            $permission = PermissionEnum::PERMIT_ACCESS;
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

    /**
     * @param string $action
     *
     * @return string
     */
    protected function convertCamelCaseToUnderscore($action)
    {
        return strtolower(preg_replace('/\B([A-Z])/', '_$1', $action));
    }
}
