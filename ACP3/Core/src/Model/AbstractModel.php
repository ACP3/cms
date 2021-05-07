<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model;

use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Core\Model\Event\ModelSavePrepareDataEvent;
use ACP3\Core\Model\Repository\AbstractRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class AbstractModel
{
    public const EVENT_PREFIX = '';

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;
    /**
     * @var AbstractRepository
     */
    protected $repository;
    /**
     * @var DataProcessor
     */
    private $dataProcessor;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        DataProcessor $dataProcessor,
        AbstractRepository $repository
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->repository = $repository;
        $this->dataProcessor = $dataProcessor;
    }

    protected function getDataProcessor(): DataProcessor
    {
        return $this->dataProcessor;
    }

    /**
     * @param int|null $entryId
     *
     * @return bool|int
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function save(array $rawData, $entryId = null)
    {
        $filteredData = $this->prepareData($rawData, $entryId);

        $isNewEntry = $entryId === null;
        $hasDataChanges = $this->hasDataChanges($filteredData, $entryId);
        $event = $this->createModelSaveEvent($entryId, $isNewEntry, $hasDataChanges, $filteredData, $rawData);

        $this->dispatchBeforeSaveEvent($this->repository, $event);

        if ($entryId === null) {
            $result = $this->repository->insert($filteredData);

            if ($result !== false) {
                $entryId = $result;
            }
        } else {
            $result = $hasDataChanges ? $this->repository->update($filteredData, $entryId) : 1;
        }

        $event = $this->createModelSaveEvent($entryId, $isNewEntry, $hasDataChanges, $filteredData, $rawData);
        $this->dispatchAfterSaveEvent(
            $this->repository,
            $event
        );

        return $result;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    protected function hasDataChanges(array $filteredData, ?int $entryId): bool
    {
        if ($entryId === null) {
            return true;
        }

        $result = $this->getOneById($entryId);
        $filteredResult = $this->dataProcessor->escape($result, $this->getAllowedColumns());

        if ($this instanceof UpdatedAtAwareModelInterface) {
            unset($result['updated_at']);
        }

        foreach ($filteredResult as $column => $value) {
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
        array $rawData = []
    ): ModelSaveEvent {
        return new ModelSaveEvent(
            static::EVENT_PREFIX,
            $filteredData,
            $rawData,
            $entryId,
            $isNewEntry,
            $hasDataChanges,
            $this->repository::TABLE_NAME
        );
    }

    /**
     * @return array
     *
     * @throws \Doctrine\DBAL\Exception
     */
    protected function prepareData(array $rawData, ?int $entryId)
    {
        $currentData = null;

        if ($entryId !== null) {
            $currentData = $this->getOneById($entryId);
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
     * @param int|array $entryId
     *
     * @return int
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function delete($entryId)
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
     * @return AbstractRepository
     */
    protected function getRepository()
    {
        return $this->repository;
    }
}
