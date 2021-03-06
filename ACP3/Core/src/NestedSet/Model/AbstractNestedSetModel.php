<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\NestedSet\Model;

use ACP3\Core\Model\AbstractModel;
use ACP3\Core\Model\DataProcessor;
use ACP3\Core\Model\Repository\AbstractRepository;
use ACP3\Core\Model\SortingAwareInterface;
use ACP3\Core\NestedSet\Operation\Delete;
use ACP3\Core\NestedSet\Operation\Edit;
use ACP3\Core\NestedSet\Operation\Insert;
use ACP3\Core\NestedSet\Operation\Sort;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @property \ACP3\Core\NestedSet\Model\Repository\NestedSetRepository $repository
 */
abstract class AbstractNestedSetModel extends AbstractModel implements SortingAwareInterface
{
    /**
     * @var \ACP3\Core\NestedSet\Operation\Insert
     */
    protected $insertOperation;
    /**
     * @var \ACP3\Core\NestedSet\Operation\Edit
     */
    protected $editOperation;
    /**
     * @var \ACP3\Core\NestedSet\Operation\Delete
     */
    protected $deleteOperation;
    /**
     * @var \ACP3\Core\NestedSet\Operation\Sort
     */
    private $sortOperation;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        DataProcessor $dataProcessor,
        AbstractRepository $repository,
        Insert $insertOperation,
        Edit $editOperation,
        Delete $deleteOperation,
        Sort $sortOperation
    ) {
        parent::__construct($eventDispatcher, $dataProcessor, $repository);

        $this->insertOperation = $insertOperation;
        $this->editOperation = $editOperation;
        $this->deleteOperation = $deleteOperation;
        $this->sortOperation = $sortOperation;
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
            $result = $this->insertOperation->execute($filteredData, $rawData['parent_id'] ?: 0);

            if ($result !== false) {
                $entryId = $result;
            }
        } else {
            $result = $this->editOperation->execute(
                $entryId,
                $filteredData['parent_id'],
                $filteredData[$this->repository::BLOCK_COLUMN_NAME] ?? 0,
                $filteredData
            );
        }

        $event = $this->createModelSaveEvent($entryId, $isNewEntry, $hasDataChanges, $filteredData, $rawData);
        $this->dispatchAfterSaveEvent($this->repository, $event);

        return $result;
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
            static::EVENT_PREFIX . '.model.' . $repository::TABLE_NAME . '.before_delete', $event
        );

        $affectedRows = 0;
        foreach ($entryId as $item) {
            $affectedRows += (int) $this->deleteOperation->execute($item);
        }

        $this->dispatchEvent('core.model.before_delete', $event);
        $this->dispatchEvent(
            static::EVENT_PREFIX . '.model.' . $repository::TABLE_NAME . '.after_delete', $event
        );

        return $affectedRows;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function moveUp(int $id): void
    {
        $this->move($id, 'up');
    }

    /**
     * {@inheritDoc}
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function moveDown(int $id): void
    {
        $this->move($id, 'down');
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    private function move(int $id, string $direction): void
    {
        $this->dispatchBeforeSaveEvent($this->getRepository(), $this->createModelSaveEvent(
            $id,
            false,
            true
        ));

        $this->sortOperation->execute($id, $direction);

        $this->dispatchAfterSaveEvent($this->getRepository(), $this->createModelSaveEvent(
            $id,
            false,
            true
        ));
    }
}
