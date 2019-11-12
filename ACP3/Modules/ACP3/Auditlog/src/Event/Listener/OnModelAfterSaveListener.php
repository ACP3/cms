<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Auditlog\Event\Listener;

use ACP3\Core\Date;
use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Core\Model\Repository\ModuleAwareRepositoryInterface;
use ACP3\Modules\ACP3\Auditlog\Model\Repository\AuditLogRepository;
use ACP3\Modules\ACP3\Users\Model\UserModel;
use Doctrine\DBAL\DBALException;
use Psr\Log\LoggerInterface;

class OnModelAfterSaveListener
{
    /**
     * @var \ACP3\Core\Date
     */
    private $date;
    /**
     * @var \ACP3\Modules\ACP3\Users\Model\UserModel
     */
    private $userModel;
    /**
     * @var \ACP3\Core\Model\Repository\ModuleAwareRepositoryInterface
     */
    private $moduleAwareRepository;
    /**
     * @var \ACP3\Modules\ACP3\Auditlog\Model\Repository\AuditLogRepository
     */
    private $auditLogRepository;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * OnModelAfterSaveListener constructor.
     */
    public function __construct(
        LoggerInterface $logger,
        Date $date,
        UserModel $userModel,
        ModuleAwareRepositoryInterface $moduleAwareRepository,
        AuditLogRepository $auditLogRepository)
    {
        $this->date = $date;
        $this->userModel = $userModel;
        $this->moduleAwareRepository = $moduleAwareRepository;
        $this->auditLogRepository = $auditLogRepository;
        $this->logger = $logger;
    }

    public function __invoke(ModelSaveEvent $event): void
    {
        if ($event->hasDataChanges() === false) {
            return;
        }

        try {
            $moduleId = $this->moduleAwareRepository->getModuleId($event->getModuleName());

            foreach ($this->prepareEntryIds($event) as $entryId) {
                $this->auditLogRepository->insert([
                    'date' => $this->date->toSQL(),
                    'module_id' => (int) $moduleId,
                    'table_name' => $event->getTableName(),
                    'entry_id' => (int) $entryId,
                    'action' => $this->getAction($event),
                    'data' => \serialize($event->getData()),
                    'user_id' => $this->userModel->isAuthenticated() ? $this->userModel->getUserId() : null,
                ]);
            }
        } catch (DBALException $e) {
            $this->logger->error($e->getMessage(), $e->getTrace());
        }
    }

    /**
     * @return array|null
     */
    private function prepareEntryIds(ModelSaveEvent $event)
    {
        $entryIds = $event->getEntryId();
        if (!\is_array($entryIds)) {
            $entryIds = [$entryIds];
        }

        return $entryIds;
    }

    private function getAction(ModelSaveEvent $event): string
    {
        if ($event->isDeleteStatement()) {
            return 'deleted';
        } elseif ($event->isIsNewEntry()) {
            return 'created';
        }

        return 'updated';
    }
}
