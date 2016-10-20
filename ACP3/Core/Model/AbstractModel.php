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
     * @param array $data
     * @param null|int $entryId
     * @return bool|int
     */
    protected function save(array $data, $entryId = null)
    {
        $this->dispatchBeforeSaveEvent($this->repository, $data, $entryId);

        $data = $this->prepareData($data);

        if (intval($entryId)) {
            $result = $this->repository->update($data, $entryId);
        } else {
            $result = $this->repository->insert($data);

            if ($result !== false) {
                $entryId = $result;
            }
        }

        $this->dispatchAfterSaveEvent($this->repository, $data, $entryId);

        return $result;
    }

    /**
     * @param AbstractRepository $repository
     * @param array $data
     * @param int|null|array $entryId
     */
    protected function dispatchBeforeSaveEvent(AbstractRepository $repository, array $data, $entryId)
    {
        $this->dispatchEvent('core.model.before_save', $data, $entryId);
        $this->dispatchEvent(
            static::EVENT_PREFIX . '.model.' . $repository::TABLE_NAME . '.before_save',
            $data,
            $entryId
        );
    }

    /**
     * @param string $eventName
     * @param array $data
     * @param int|null|array $entryId
     */
    protected function dispatchEvent($eventName, array $data, $entryId)
    {
        $this->eventDispatcher->dispatch(
            $eventName,
            new ModelSaveEvent($data, $entryId)
        );
    }

    /**
     * @param array $data
     * @return array
     */
    protected function prepareData(array $data)
    {
        return $this->dataProcessor->processColumnData($data, $this->getAllowedColumns());
    }

    /**
     * @return array
     */
    abstract protected function getAllowedColumns();

    /**
     * @param AbstractRepository $repository
     * @param array $data
     * @param int|null|array $entryId
     */
    protected function dispatchAfterSaveEvent(AbstractRepository $repository, array $data, $entryId)
    {
        $this->dispatchEvent('core.model.after_save', $data, $entryId);
        $this->dispatchEvent(
            static::EVENT_PREFIX . '.model.' . $repository::TABLE_NAME . '.after_save',
            $data,
            $entryId
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

        $this->dispatchEvent(
            'core.model.before_delete',
            [],
            $entryId
        );
        $this->dispatchEvent(
            static::EVENT_PREFIX . '.model.' . $repository::TABLE_NAME . '.before_delete',
            [],
            $entryId
        );

        $affectedRows = 0;
        foreach ($entryId as $item) {
            $affectedRows += (int)$this->repository->delete($item);
        }

        $this->dispatchEvent(
            'core.model.before_delete',
            [],
            $entryId
        );
        $this->dispatchEvent(
            static::EVENT_PREFIX . '.model.' . $repository::TABLE_NAME . '.after_delete',
            [],
            $entryId
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
