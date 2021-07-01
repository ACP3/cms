<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Model;

use ACP3\Core\Cache;
use ACP3\Core\Model\AbstractModel;
use ACP3\Core\Model\DataProcessor;
use ACP3\Modules\ACP3\Permissions\Installer\Schema;
use ACP3\Modules\ACP3\Permissions\Model\Repository\RuleRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @method RuleRepository getRepository()
 */
class RulesModel extends AbstractModel
{
    public const EVENT_PREFIX = Schema::MODULE_NAME;

    /**
     * @var Cache
     */
    private $aclCache;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        DataProcessor $dataProcessor,
        RuleRepository $repository,
        Cache $aclCache
    ) {
        parent::__construct($eventDispatcher, $dataProcessor, $repository);

        $this->aclCache = $aclCache;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function updateRules(array $privileges, int $roleId): void
    {
        $rules = $this->getRepository()->getAllRulesByRoleIds([$roleId]);

        foreach ($privileges as $moduleId => $modulePrivileges) {
            foreach ($modulePrivileges as $privilegeId => $permission) {
                $ruleUpsertValues = [
                    'role_id' => $roleId,
                    'module_id' => $moduleId,
                    'privilege_id' => $privilegeId,
                    'permission' => $permission,
                ];

                $this->save($ruleUpsertValues, $this->findRuleId($rules, $moduleId, $privilegeId));
            }
        }

        $this->aclCache->deleteAll();
    }

    private function findRuleId(array $rules, int $moduleId, int $privilegeId): ?int
    {
        foreach ($rules as $rule) {
            if ((int) $rule['module_id'] === $moduleId && (int) $rule['privilege_id'] === $privilegeId) {
                return (int) $rule['id'];
            }
        }

        return null;
    }

    protected function getAllowedColumns(): array
    {
        return [
            'role_id' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT,
            'module_id' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT,
            'privilege_id' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT,
            'permission' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT,
        ];
    }
}
