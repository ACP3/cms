<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Auditlog\EventListener;

use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Core\Date;
use ACP3\Core\Model\Event\AfterModelDeleteEvent;
use ACP3\Core\Model\Event\AfterModelSaveEvent;
use ACP3\Core\Repository\ModuleAwareRepositoryInterface;
use ACP3\Modules\ACP3\Auditlog\Repository\AuditLogRepository;
use Doctrine\DBAL\Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnModelAfterSaveListener implements EventSubscriberInterface
{
    public function __construct(private readonly LoggerInterface $logger, private readonly Date $date, private readonly UserModelInterface $userModel, private readonly ModuleAwareRepositoryInterface $moduleAwareRepository, private readonly AuditLogRepository $auditLogRepository)
    {
    }

    public function __invoke(AfterModelSaveEvent|AfterModelDeleteEvent $event): void
    {
        if ($event->hasDataChanges() === false) {
            return;
        }

        try {
            $moduleId = $this->moduleAwareRepository->getModuleId($event->getModuleName());

            foreach ($this->prepareEntryIds($event) as $entryId) {
                $this->auditLogRepository->insert([
                    'date' => $this->date->toSQL(),
                    'module_id' => $moduleId,
                    'table_name' => $event->getTableName(),
                    'entry_id' => $entryId,
                    'action' => $this->getAction($event),
                    'data' => serialize($event->getData()),
                    'user_id' => $this->userModel->isAuthenticated() ? $this->userModel->getUserId() : null,
                ]);
            }
        } catch (Exception $e) {
            $this->logger->error($e->getMessage(), $e->getTrace());
        }
    }

    /**
     * @return int[]|array<string, string|int>
     */
    private function prepareEntryIds(AfterModelDeleteEvent|AfterModelSaveEvent $event): array
    {
        return (array) $event->getEntryId();
    }

    private function getAction(AfterModelDeleteEvent|AfterModelSaveEvent $event): string
    {
        if ($event instanceof AfterModelDeleteEvent) {
            return 'deleted';
        }
        if ($event->isIsNewEntry()) {
            return 'created';
        }

        return 'updated';
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            AfterModelSaveEvent::class => ['__invoke', -255],
            AfterModelDeleteEvent::class => ['__invoke', -255],
        ];
    }
}
