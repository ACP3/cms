<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Model;

use ACP3\Core\Model\AbstractModel;
use ACP3\Core\Model\DataProcessor;
use ACP3\Modules\ACP3\Permissions\Cache;
use ACP3\Modules\ACP3\Permissions\Installer\Schema;
use ACP3\Modules\ACP3\Permissions\Model\Repository\RuleRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @method RuleRepository getRepository()
 */
class RulesModel extends AbstractModel
{
    const EVENT_PREFIX = Schema::MODULE_NAME;

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * RulesModel constructor.
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        DataProcessor $dataProcessor,
        RuleRepository $repository,
        Cache $cache
    ) {
        parent::__construct($eventDispatcher, $dataProcessor, $repository);

        $this->cache = $cache;
    }

    /**
     * @param int $roleId
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function updateRules(array $privileges, $roleId)
    {
        $rules = $this->getRepository()->getAllRulesByRoleIds([$roleId]);

        foreach ($privileges as $moduleId => $modulePrivileges) {
            foreach ($modulePrivileges as $privilegeId => $permission) {
                $ruleInsertValues = [
                    'role_id' => $roleId,
                    'module_id' => $moduleId,
                    'privilege_id' => $privilegeId,
                    'permission' => $permission,
                ];

                $this->save($ruleInsertValues, $this->findRuleId($rules, $moduleId, $privilegeId));
            }
        }

        $this->cache->getCacheDriver()->deleteAll();
    }

    private function findRuleId(array $rules, int $moduleId, int $privilegeId): ?int
    {
        foreach ($rules as $rule) {
            if ($rule['module_id'] == $moduleId && $rule['privilege_id'] == $privilegeId) {
                return $rule['id'];
            }
        }

        return null;
    }

    /**
     * @return array
     */
    protected function getAllowedColumns()
    {
        return [
            'role_id' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT,
            'module_id' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT,
            'privilege_id' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT,
            'permission' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT,
        ];
    }
}