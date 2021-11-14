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

    public function __construct(protected EventDispatcherInterface $eventDispatcher, private DataProcessor $dataProcessor, protected AbstractRepository $repository)
    {
    }

    protected function getDataProcessor(): DataProcessor
    {
        return $this->dataProcessor;
    }

    /**
     * @param int|null $entryId
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function save(array $rawData, $entryId = null): int
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

    protected function loadCurrentData(?int $entryId): ?array
    {
        if ($entryId === null) {
            return null;
        }

        $result = $this->getOneById($entryId);

        return $this->dataProcessor->escape($result, $this->getAllowedColumns());
    }

    /**
     * @throws \Doctrine\DBAL\Exception
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
    ) {
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
        ModelSaveEvent $event)
    {
        $this->eventDispatcher->dispatch(
            $event,
            $eventName
        );
    }

    protected function createModelSaveEvent(
        $entryId,
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
     * @return array
     *
     * @throws \Doctrine\DBAL\Exception
     */
    protected function prepareData(array $rawData, ?array $currentData)
    {
        if ($currentData !== null) {
            $rawData = array_merge($currentData, $rawData);
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
     * @return array
     */
    abstract protected function getAllowedColumns();

    protected function dispatchAfterSaveEvent(
        AbstractRepository $repository,
        ModelSaveEvent $event
    ) {
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
     * @return array<int>
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
     * @return int
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function delete(array|int $entryId)
    {
        $repository = $this->repository;

        if (!\is_array($entryId)) {
            $entryId = [$entryId];
        }

        $event = $this->createModelSaveEvent($entryId, false, true);

        $this->dispatchEvent('core.model.before_delete', $event);
        $this->dispatchEvent(
            static::EVENT_PREFIX . '.model.' . $repository::TABLE_NAME . '.before_delete',
            $event
        );

        $affectedRows = 0;
        foreach ($entryId as $item) {
            $affectedRows += (int) $this->repository->delete($item);
        }

        $this->dispatchEvent('core.model.after_delete', $event);
        $this->dispatchEvent(
            static::EVENT_PREFIX . '.model.' . $repository::TABLE_NAME . '.after_delete',
            $event
        );

        return $affectedRows;
    }

    /**
     * @return array
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getOneById(int $entryId)
    {
        return $this->repository->getOneById($entryId);
    }

    /**
     * @return \ACP3\Core\Repository\AbstractRepository
     */
    protected function getRepository()
    {
        return $this->repository;
    }
}
