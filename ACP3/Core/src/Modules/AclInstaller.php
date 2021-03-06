<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Modules;

use ACP3\Core\ACL\Model\Repository\PrivilegeRepositoryInterface;
use ACP3\Core\ACL\Model\Repository\RoleRepositoryInterface;
use ACP3\Core\ACL\PermissionEnum;
use ACP3\Core\ACL\PrivilegeEnum;
use ACP3\Core\Cache;
use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Model\Repository\AbstractRepository;
use ACP3\Core\Modules\Installer\SchemaInterface;

class AclInstaller implements InstallerInterface
{
    public const INSTALL_RESOURCES_AND_RULES = 1;
    public const INSTALL_RESOURCES = 2;

    /**
     * @var \ACP3\Core\Cache
     */
    private $aclCache;
    /**
     * @var \ACP3\Core\Modules\SchemaHelper
     */
    private $schemaHelper;
    /**
     * @var \ACP3\Core\ACL\Model\Repository\RoleRepositoryInterface
     */
    private $roleRepository;
    /**
     * @var \ACP3\Core\ACL\Model\Repository\PrivilegeRepositoryInterface
     */
    private $privilegeRepository;
    /**
     * @var \ACP3\Core\Model\Repository\AbstractRepository
     */
    private $resourceRepository;
    /**
     * @var \ACP3\Core\Model\Repository\AbstractRepository
     */
    private $ruleRepository;

    public function __construct(
        Cache $aclCache,
        SchemaHelper $schemaHelper,
        RoleRepositoryInterface $roleRepository,
        AbstractRepository $ruleRepository,
        AbstractRepository $resourceRepository,
        PrivilegeRepositoryInterface $privilegeRepository
    ) {
        $this->aclCache = $aclCache;
        $this->schemaHelper = $schemaHelper;
        $this->roleRepository = $roleRepository;
        $this->ruleRepository = $ruleRepository;
        $this->resourceRepository = $resourceRepository;
        $this->privilegeRepository = $privilegeRepository;
    }

    /**
     * Fügt die zu einen Modul zugehörigen Ressourcen ein.
     *
     * @param int $mode
     *
     * @return bool
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function install(SchemaInterface $schema, $mode = self::INSTALL_RESOURCES_AND_RULES)
    {
        $this->insertAclResources($schema);

        if ($mode === self::INSTALL_RESOURCES_AND_RULES) {
            $this->insertAclRules($schema->getModuleName());
        }

        $this->aclCache->getDriver()->deleteAll();

        return true;
    }

    /**
     * Inserts a new resource into the database.
     *
     * @throws \Doctrine\DBAL\Exception
     */
    private function insertAclResources(SchemaInterface $schema): void
    {
        $moduleId = $this->schemaHelper->getModuleId($schema->getModuleName());

        foreach ($schema->specialResources() as $area => $controllers) {
            foreach ($controllers as $controller => $actions) {
                foreach ($actions as $action => $privilegeId) {
                    $insertValues = [
                        'module_id' => $moduleId,
                        'area' => !empty($area) ? \strtolower($area) : AreaEnum::AREA_FRONTEND,
                        'controller' => \strtolower($controller),
                        'page' => $this->convertCamelCaseToUnderscore($action),
                        'params' => '',
                        'privilege_id' => (int) $privilegeId,
                    ];
                    $this->resourceRepository->insert($insertValues);
                }
            }
        }
    }

    private function convertCamelCaseToUnderscore(string $action): string
    {
        return \strtolower(\preg_replace('/\B([A-Z])/', '_$1', $action));
    }

    /**
     * Insert new acl user rules.
     *
     * @throws \Doctrine\DBAL\Exception
     */
    private function insertAclRules(string $moduleName): void
    {
        $roles = $this->roleRepository->getAllRoles();
        $privileges = $this->privilegeRepository->getAllPrivilegeIds();
        $moduleId = $this->schemaHelper->getModuleId($moduleName);

        foreach ($roles as $role) {
            foreach ($privileges as $privilege) {
                $insertValues = [
                    'role_id' => $role['id'],
                    'module_id' => $moduleId,
                    'privilege_id' => $privilege['id'],
                    'permission' => $this->getDefaultAclRulePermission($role, $privilege),
                ];
                $this->ruleRepository->insert($insertValues);
            }
        }
    }

    private function getDefaultAclRulePermission(array $role, array $privilege): int
    {
        $permission = PermissionEnum::DENY_ACCESS;
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
     * Löscht die zu einem Modul zugehörigen Ressourcen.
     */
    public function uninstall(SchemaInterface $schema): bool
    {
        $this->aclCache->getDriver()->deleteAll();

        return true;
    }
}
