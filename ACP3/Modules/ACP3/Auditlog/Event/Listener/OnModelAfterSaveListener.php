<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Auditlog\Event\Listener;

use ACP3\Core\Date;
use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Core\Model\Repository\ModuleAwareRepositoryInterface;
use ACP3\Modules\ACP3\Auditlog\Model\Repository\AuditlogRepository;
use ACP3\Modules\ACP3\Users\Model\UserModel;

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
     * @var \ACP3\Modules\ACP3\Auditlog\Model\Repository\AuditlogRepository
     */
    private $auditlogRepository;

    /**
     * OnModelAfterSaveListener constructor.
     *
     * @param \ACP3\Core\Date                                                 $date
     * @param \ACP3\Modules\ACP3\Users\Model\UserModel                        $userModel
     * @param \ACP3\Core\Model\Repository\ModuleAwareRepositoryInterface      $moduleAwareRepository
     * @param \ACP3\Modules\ACP3\Auditlog\Model\Repository\AuditlogRepository $auditlogRepository
     */
    public function __construct(
        Date $date,
        UserModel $userModel,
        ModuleAwareRepositoryInterface $moduleAwareRepository,
        AuditlogRepository $auditlogRepository)
    {
        $this->date = $date;
        $this->userModel = $userModel;
        $this->moduleAwareRepository = $moduleAwareRepository;
        $this->auditlogRepository = $auditlogRepository;
    }

    public function onModelSave(ModelSaveEvent $event): void
    {
        if ($event->hasDataChanges() === true) {
            $this->auditlogRepository->insert([
                'date' => $this->date->toSQL(),
                'module_id' => (int) $this->moduleAwareRepository->getModuleId($event->getModuleName()),
                'entry_id' => (int) $event->getEntryId(),
                'action' => $this->getAction($event),
                'data' => \serialize($event->getData()),
                'user_id' => $this->userModel->getUserId(),
            ]);
        }
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
