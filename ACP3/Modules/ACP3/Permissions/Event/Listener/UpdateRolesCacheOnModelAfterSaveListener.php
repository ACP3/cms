<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Event\Listener;


use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Modules\ACP3\Permissions\Cache;
use ACP3\Modules\ACP3\Permissions\Model\Repository\RuleRepository;

class UpdateRolesCacheOnModelAfterSaveListener
{
    /**
     * @var Cache
     */
    protected $cache;
    /**
     * @var RuleRepository
     */
    protected $ruleRepository;

    /**
     * UpdateRolesCacheOnModelAfterSaveListener constructor.
     * @param Cache $cache
     * @param RuleRepository $ruleRepository
     */
    public function __construct(Cache $cache, RuleRepository $ruleRepository)
    {
        $this->cache = $cache;
        $this->ruleRepository = $ruleRepository;
    }

    /**
     * @param ModelSaveEvent $event
     */
    public function execute(ModelSaveEvent $event)
    {
        $data = $event->getData();
        $this->ruleRepository->delete($event->getEntryId(), 'role_id');

        $this->saveRules($data['privileges'], $event->getEntryId());

        $this->cache->getCacheDriver()->deleteAll();
    }

    /**
     * @param array $privileges
     * @param int   $roleId
     */
    protected function saveRules(array $privileges, $roleId)
    {
        foreach ($privileges as $moduleId => $modulePrivileges) {
            foreach ($modulePrivileges as $privilegeId => $permission) {
                $ruleInsertValues = [
                    'id' => '',
                    'role_id' => $roleId,
                    'module_id' => $moduleId,
                    'privilege_id' => $privilegeId,
                    'permission' => $permission
                ];
                $this->ruleRepository->insert($ruleInsertValues);
            }
        }
    }
}
