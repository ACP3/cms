<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Model;


use ACP3\Core\Model\Repository\AbstractRepository;
use ACP3\Core\NestedSet\Operation\Delete;
use ACP3\Core\NestedSet\Operation\Edit;
use ACP3\Core\NestedSet\Operation\Insert;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class AbstractNestedSetModel extends AbstractModel
{
    /**
     * @var Insert
     */
    protected $insertOperation;
    /**
     * @var Edit
     */
    protected $editOperation;
    /**
     * @var
     */
    protected $deleteOperation;

    /**
     * AbstractNestedSetModel constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param DataProcessor $dataProcessor
     * @param AbstractRepository $repository
     * @param Insert $insertOperation
     * @param Edit $editOperation
     * @param Delete $deleteOperation
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        DataProcessor $dataProcessor,
        AbstractRepository $repository,
        Insert $insertOperation,
        Edit $editOperation,
        Delete $deleteOperation
    ) {
        parent::__construct($eventDispatcher, $dataProcessor, $repository);

        $this->insertOperation = $insertOperation;
        $this->editOperation = $editOperation;
        $this->deleteOperation = $deleteOperation;
    }

    /**
     * @param array $columnData
     * @param int|null $entryId
     * @return bool|int
     */
    public function save(array $columnData, $entryId = null)
    {
        $columnData = $this->prepareData($columnData);

        $this->dispatchBeforeSaveEvent($this->repository, $columnData, $entryId);

        if ($entryId === null) {
            $result = $this->insertOperation->execute($columnData, $columnData['parent_id']);

            if ($result !== false) {
                $entryId = $result;
            }
        } else {
            $result = $this->editOperation->execute(
                $entryId,
                $columnData['parent_id'],
                isset($columnData['block_id']) ? $columnData['block_id'] : 0,
                $columnData
            );
        }

        $this->dispatchAfterSaveEvent($this->repository, $columnData, $entryId);

        return $result;
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
            $affectedRows += (int)$this->deleteOperation->execute($item);
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
}
