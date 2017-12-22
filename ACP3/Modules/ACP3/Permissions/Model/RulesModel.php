<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Model;

use ACP3\Core\Model\AbstractModel;
use ACP3\Core\Model\DataProcessor;
use ACP3\Modules\ACP3\Permissions\Cache;
use ACP3\Modules\ACP3\Permissions\Installer\Schema;
use ACP3\Modules\ACP3\Permissions\Model\Repository\RuleRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class RulesModel extends AbstractModel
{
    const EVENT_PREFIX = Schema::MODULE_NAME;

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * RulesModel constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param DataProcessor $dataProcessor
     * @param RuleRepository $repository
     * @param Cache $cache
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        DataProcessor $dataProcessor,
        RuleRepository $repository,
        Cache $cache)
    {
        parent::__construct($eventDispatcher, $dataProcessor, $repository);

        $this->cache = $cache;
    }

    /**
     * @param array $privileges
     * @param int   $roleId
     */
    public function updateRules(array $privileges, $roleId)
    {
        $this->repository->delete($roleId, 'role_id');

        $this->cache->getCacheDriver()->deleteAll();

        foreach ($privileges as $moduleId => $modulePrivileges) {
            foreach ($modulePrivileges as $privilegeId => $permission) {
                $ruleInsertValues = [
                    'role_id' => $roleId,
                    'module_id' => $moduleId,
                    'privilege_id' => $privilegeId,
                    'permission' => $permission
                ];

                $this->save($ruleInsertValues);
            }
        }
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
            'permission' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT
        ];
    }
}
