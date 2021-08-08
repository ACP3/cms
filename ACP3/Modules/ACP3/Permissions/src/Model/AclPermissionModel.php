<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Model;

use ACP3\Core\Model\AbstractModel;
use ACP3\Core\Model\DataProcessor;
use ACP3\Modules\ACP3\Permissions\Installer\Schema;
use ACP3\Modules\ACP3\Permissions\Repository\AclPermissionRepository;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @method AclPermissionRepository getRepository()
 */
class AclPermissionModel extends AbstractModel
{
    public const EVENT_PREFIX = Schema::MODULE_NAME;

    /**
     * @var CacheItemPoolInterface
     */
    private $permissionsCachePool;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        DataProcessor $dataProcessor,
        AclPermissionRepository $repository,
        CacheItemPoolInterface $permissionsCachePool
    ) {
        parent::__construct($eventDispatcher, $dataProcessor, $repository);

        $this->permissionsCachePool = $permissionsCachePool;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function updatePermissions(array $resources, int $roleId): void
    {
        $permissionsByRoleIds = $this->getRepository()->getPermissionsByRoleIds([$roleId]);

        foreach ($resources as $resourceId => $permissionValue) {
            $permissionUpsertValues = [
                'role_id' => $roleId,
                'resource_id' => $resourceId,
                'permission' => $permissionValue,
            ];

            $this->save($permissionUpsertValues, $this->findRuleId($permissionsByRoleIds, $resourceId, $roleId));
        }

        $this->permissionsCachePool->clear();
    }

    private function findRuleId(array $permissions, int $resourceId, int $roleId): ?int
    {
        foreach ($permissions as $permission) {
            if ((int) $permission['resource_id'] === $resourceId && (int) $permission['role_id'] === $roleId) {
                return (int) $permission['id'];
            }
        }

        return null;
    }

    protected function getAllowedColumns(): array
    {
        return [
            'role_id' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT,
            'resource_id' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT,
            'permission' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT,
        ];
    }
}
