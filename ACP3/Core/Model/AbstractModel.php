<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Model;

use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Core\Model\Repository\AbstractRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class AbstractModel
 * @package ACP3\Core\Model
 */
abstract class AbstractModel
{
    const EVENT_PREFIX = '';

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

    /**
     * AbstractModel constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param DataProcessor $dataProcessor
     * @param AbstractRepository $repository
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        DataProcessor $dataProcessor,
        AbstractRepository $repository
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->repository = $repository;
        $this->dataProcessor = $dataProcessor;
    }

    /**
     * @param array $rawData
     * @param null|int $entryId
     * @return bool|int
     */
    public function save(array $rawData, $entryId = null)
    {
        $filteredData = $this->prepareData($rawData);

        $this->dispatchBeforeSaveEvent($this->repository, $entryId, $filteredData, $rawData);

        if ($entryId === null) {
            $result = $this->repository->insert($filteredData);

            if ($result !== false) {
                $entryId = $result;
            }
        } else {
            $result = $this->repository->update($filteredData, $entryId);
        }

        $this->dispatchAfterSaveEvent($this->repository, $entryId, $filteredData, $rawData);

        return $result;
    }

    /**
     * @param AbstractRepository $repository
     * @param int|null|array $entryId
     * @param array $filteredData
     * @param array $rawData
     */
    protected function dispatchBeforeSaveEvent(
        AbstractRepository $repository,
        $entryId,
        array $filteredData,
        array $rawData
    ) {
        $this->dispatchEvent('core.model.before_save', $entryId, $filteredData, $rawData);
        $this->dispatchEvent(
            static::EVENT_PREFIX . '.model.' . $repository::TABLE_NAME . '.before_save',
            $entryId,
            $filteredData,
            $rawData
        );
    }

    /**
     * @param string $eventName
     * @param int|null|array $entryId
     * @param array $filteredData
     * @param array $rawData
     */
    protected function dispatchEvent($eventName, $entryId, array $filteredData = [], array $rawData = [])
    {
        $this->eventDispatcher->dispatch(
            $eventName,
            new ModelSaveEvent(static::EVENT_PREFIX, $filteredData, $rawData, $entryId)
        );
    }

    /**
     * @param array $rawData
     * @return array
     */
    protected function prepareData(array $rawData)
    {
        return $this->dataProcessor->processColumnData($rawData, $this->getAllowedColumns());
    }

    /**
     * @return array
     */
    abstract protected function getAllowedColumns();

    /**
     * @param AbstractRepository $repository
     * @param int|null|array $entryId
     * @param array $filteredData
     * @param array $rawData
     */
    protected function dispatchAfterSaveEvent(
        AbstractRepository $repository,
        $entryId,
        array $filteredData,
        array $rawData
    ) {
        $this->dispatchEvent('core.model.after_save', $entryId, $filteredData, $rawData);
        $this->dispatchEvent(
            static::EVENT_PREFIX . '.model.' . $repository::TABLE_NAME . '.after_save',
            $entryId,
            $filteredData,
            $rawData
        );
    }

    /**
     * @param int|array $entryId
     * @return int
     */
    public function delete($entryId)
    {
        $repository = $this->repository;

        if (!is_array($entryId)) {
            $entryId = [$entryId];
        }

        $this->dispatchEvent('core.model.before_delete', $entryId);
        $this->dispatchEvent(
            static::EVENT_PREFIX . '.model.' . $repository::TABLE_NAME . '.before_delete', $entryId
        );

        $affectedRows = 0;
        foreach ($entryId as $item) {
            $affectedRows += (int)$this->repository->delete($item);
        }

        $this->dispatchEvent('core.model.before_delete', $entryId);
        $this->dispatchEvent(
            static::EVENT_PREFIX . '.model.' . $repository::TABLE_NAME . '.after_delete', $entryId
        );

        return $affectedRows;
    }

    /**
     * @param int $entryId
     * @return array
     */
    public function getOneById($entryId)
    {
        return $this->repository->getOneById($entryId);
    }
}
