<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model;

use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Core\Model\Event\ModelSavePrepareDataEvent;
use ACP3\Core\Repository\AbstractRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class AbstractModel
{
    public const EVENT_PREFIX = '';

    public function __construct(protected EventDispatcherInterface $eventDispatcher, private readonly DataProcessor $dataProcessor, protected AbstractRepository $repository)
    {
    }

    protected function getDataProcessor(): DataProcessor
    {
        return $this->dataProcessor;
    }

    /**
     * @param array<string, mixed> $rawData
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function save(array $rawData, ?int $entryId = null): int
    {
        $currentData = $this->loadCurrentData($entryId);
        $filteredNewData = $this->prepareData($rawData, $currentData);

        $isNewEntry = $entryId === null;
        $hasDataChanges = $this->hasDataChanges($filteredNewData, $currentData);
        $event = $this->createModelSaveEvent($entryId, $isNewEntry, $hasDataChanges, $filteredNewData, $rawData, $currentData);

        $this->dispatchBeforeSaveEvent($this->repository, $event);

        [$entryId, $result] = $this->doUpsert($entryId, $filteredNewData, $hasDataChanges);

        $event = $this->createModelSaveEvent($entryId, $isNewEntry, $hasDataChanges, $filteredNewData, $rawData, $currentData);
        $this->dispatchAfterSaveEvent(
            $this->repository,
            $event
        );

        return $result;
    }

    /**
     * @return array<string, mixed>|null
     *
     * @throws \Doctrine\DBAL\Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function loadCurrentData(?int $entryId): ?array
    {
        if ($entryId === null) {
            return null;
        }

        $result = $this->getOneById($entryId);

        return $this->dataProcessor->escape($result, $this->getAllowedColumns());
    }

    /**
     * @param array<string, mixed>      $filteredData
     * @param array<string, mixed>|null $currentData
     */
    protected function hasDataChanges(array $filteredData, ?array $currentData): bool
    {
        if ($currentData === null) {
            return true;
        }

        if ($this instanceof UpdatedAtAwareModelInterface) {
            unset($currentData['updated_at']);
        }

        foreach ($currentData as $column => $value) {
            if (\array_key_exists($column, $filteredData) && $filteredData[$column] !== $value) {
                return true;
            }
        }

        return false;
    }

    protected function dispatchBeforeSaveEvent(
        AbstractRepository $repository,
        ModelSaveEvent $event
    ): void {
        $this->dispatchEvent(
            'core.model.before_save',
            $event
        );
        $this->dispatchEvent(
            static::EVENT_PREFIX . '.model.' . $repository::TABLE_NAME . '.before_save',
            $event
        );
    }

    protected function dispatchEvent(
        string $eventName,
        ModelSaveEvent $event): void
    {
        $this->eventDispatcher->dispatch(
            $event,
            $eventName
        );
    }

    /**
     * @param array<string, string|int>|int|null $entryId
     * @param array<string, mixed>               $filteredData
     * @param array<string, mixed>               $rawData
     * @param array<string, mixed>|null          $currentData
     */
    protected function createModelSaveEvent(
        array|int|null $entryId,
        bool $isNewEntry,
        bool $hasDataChanges,
        array $filteredData = [],
        array $rawData = [],
        ?array $currentData = null
    ): ModelSaveEvent {
        return new ModelSaveEvent(
            static::EVENT_PREFIX,
            $filteredData,
            $rawData,
            $entryId,
            $isNewEntry,
            $hasDataChanges,
            $this->repository::TABLE_NAME,
            $currentData
        );
    }

    /**
     * @param array<string, mixed>      $rawData
     * @param array<string, mixed>|null $currentData
     *
     * @return array<string, mixed>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    protected function prepareData(array $rawData, ?array $currentData): array
    {
        if ($currentData !== null) {
            $rawData = [...$currentData, ...$rawData];
        }

        $modelSavePrepareDataEvent = new ModelSavePrepareDataEvent($rawData, $currentData, $this->getAllowedColumns());

        $this->eventDispatcher->dispatch(
            $modelSavePrepareDataEvent,
            static::EVENT_PREFIX . '.model.' . $this->repository::TABLE_NAME . '.prepare_data'
        );

        return $this->dataProcessor->escape(
            $modelSavePrepareDataEvent->getRawData(),
            $modelSavePrepareDataEvent->getAllowedColumns()
        );
    }

    /**
     * @return array<string, class-string>
     */
    abstract protected function getAllowedColumns(): array;

    protected function dispatchAfterSaveEvent(
        AbstractRepository $repository,
        ModelSaveEvent $event
    ): void {
        $this->dispatchEvent(
            'core.model.after_save',
            $event
        );
        $this->dispatchEvent(
            static::EVENT_PREFIX . '.model.' . $repository::TABLE_NAME . '.after_save',
            $event
        );
    }

    /**
     * @param array<string, mixed> $filteredNewData
     *
     * @return int[]
     *
     * @throws \Doctrine\DBAL\Exception
     */
    protected function doUpsert(?int $entryId, array $filteredNewData, bool $hasDataChanges): array
    {
        if ($entryId === null) {
            $result = $this->repository->insert($filteredNewData);

            if ($result !== false) {
                $entryId = $result;
            }
        } else {
            $result = $hasDataChanges ? $this->repository->update($filteredNewData, $entryId) : 1;
        }

        return [
            $entryId,
            $result,
        ];
    }

    /**
     * @param int[]|int $entryId
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function delete(array|int $entryId): int
    {
        if (!\is_array($entryId)) {
            $entryId = [$entryId];
        }

        $event = $this->createModelSaveEvent($entryId, false, true);

        $this->dispatchEvent('core.model.before_delete', $event);
        $this->dispatchEvent(
            static::EVENT_PREFIX . '.model.' . $this->repository::TABLE_NAME . '.before_delete',
            $event
        );

        $affectedRows = 0;
        foreach ($entryId as $item) {
            $affectedRows += $this->repository->delete($item);
        }

        $this->dispatchEvent('core.model.after_delete', $event);
        $this->dispatchEvent(
            static::EVENT_PREFIX . '.model.' . $this->repository::TABLE_NAME . '.after_delete',
            $event
        );

        return $affectedRows;
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getOneById(int $entryId): array
    {
        return $this->repository->getOneById($entryId);
    }

    protected function getRepository(): AbstractRepository
    {
        return $this->repository;
    }
}
