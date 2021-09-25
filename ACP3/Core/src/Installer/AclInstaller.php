<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Installer;

use ACP3\Core\ACL\PermissionEnum;
use ACP3\Core\ACL\PrivilegeEnum;
use ACP3\Core\ACL\Repository\AclPermissionRepositoryInterface;
use ACP3\Core\ACL\Repository\RoleRepositoryInterface;
use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Modules\InstallerInterface;
use ACP3\Core\Repository\AbstractRepository;

class AclInstaller implements InstallerInterface
{
    public const INSTALL_RESOURCES_AND_RULES = 1;
    public const INSTALL_RESOURCES = 2;

    /**
     * @var \ACP3\Core\Installer\SchemaHelper
     */
    private $schemaHelper;
    /**
     * @var \ACP3\Core\Repository\AbstractRepository
     */
    private $resourceRepository;
    /**
     * @var RoleRepositoryInterface
     */
    private $roleRepository;
    /**
     * @var AclPermissionRepositoryInterface
     */
    private $permissionRepository;

    public function __construct(
        SchemaHelper $schemaHelper,
        RoleRepositoryInterface $roleRepository,
        AbstractRepository $resourceRepository,
        AclPermissionRepositoryInterface $permissionRepository
    ) {
        $this->schemaHelper = $schemaHelper;
        $this->resourceRepository = $resourceRepository;
        $this->roleRepository = $roleRepository;
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * Fügt die zu einen Modul zugehörigen Ressourcen ein.
     *
     * @return bool
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function install(SchemaInterface $schema, int $mode = self::INSTALL_RESOURCES_AND_RULES)
    {
        $roles = $this->roleRepository->getAllRoles();
        $moduleId = $this->schemaHelper->getModuleId($schema->getModuleName());

        foreach ($schema->specialResources() as $area => $controllers) {
            foreach ($controllers as $controller => $actions) {
                foreach ($actions as $action => $privilegeId) {
                    $insertValues = [
                        'module_id' => $moduleId,
                        'area' => !empty($area) ? strtolower($area) : AreaEnum::AREA_FRONTEND,
                        'controller' => strtolower($controller),
                        'page' => $this->convertCamelCaseToUnderscore($action),
                        'params' => '',
                    ];
                    $resourceId = $this->resourceRepository->insert($insertValues);

                    if ($mode === self::INSTALL_RESOURCES_AND_RULES) {
                        foreach ($roles as $role) {
                            $permissionData = [
                                'role_id' => $role['id'],
                                'resource_id' => $resourceId,
                                'permission' => $this->getDefaultAclRulePermission($role['id'], $privilegeId),
                            ];

                            $this->permissionRepository->insert($permissionData);
                        }
                    }
                }
            }
        }

        return true;
    }

    private function convertCamelCaseToUnderscore(string $action): string
    {
        return strtolower(preg_replace('/\B([A-Z])/', '_$1', $action));
    }

    private function getDefaultAclRulePermission(int $roleId, int $privilegeId): int
    {
        if ($roleId === 1 &&
            ($privilegeId === PrivilegeEnum::FRONTEND_VIEW || $privilegeId === PrivilegeEnum::FRONTEND_CREATE)
        ) {
            return PermissionEnum::PERMIT_ACCESS;
        }
        if ($roleId === 3 && $privilegeId === PrivilegeEnum::ADMIN_VIEW) {
            return PermissionEnum::PERMIT_ACCESS;
        }
        if ($roleId > 1 && $roleId < 4) {
            return PermissionEnum::INHERIT_ACCESS;
        }
        if ($roleId === 4) {
            return PermissionEnum::PERMIT_ACCESS;
        }

        return PermissionEnum::INHERIT_ACCESS;
    }

    /**
     * Löscht die zu einem Modul zugehörigen Ressourcen.
     */
    public function uninstall(SchemaInterface $schema): bool
    {
        return true;
    }
}
