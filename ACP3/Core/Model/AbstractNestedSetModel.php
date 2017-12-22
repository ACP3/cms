<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
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
     * @param array $rawData
     * @param int|null $entryId
     * @return bool|int
     */
    public function save(array $rawData, $entryId = null)
    {
        $filteredData = $this->prepareData($rawData);

        $isNewEntry = $entryId === null;

        $this->dispatchBeforeSaveEvent($this->repository, $entryId, $filteredData, $rawData, $isNewEntry);

        if ($entryId === null) {
            $result = $this->insertOperation->execute($filteredData, $rawData['parent_id']);

            if ($result !== false) {
                $entryId = $result;
            }
        } else {
            $result = $this->editOperation->execute(
                $entryId,
                $filteredData['parent_id'],
                isset($filteredData['block_id']) ? $filteredData['block_id'] : 0,
                $filteredData
            );
        }

        $this->dispatchAfterSaveEvent($this->repository, $entryId, $filteredData, $rawData, $isNewEntry);

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

        $this->dispatchEvent('core.model.before_delete', $entryId, false);
        $this->dispatchEvent(
            static::EVENT_PREFIX . '.model.' . $repository::TABLE_NAME . '.before_delete', $entryId, false
        );

        $affectedRows = 0;
        foreach ($entryId as $item) {
            $affectedRows += (int)$this->deleteOperation->execute($item);
        }

        $this->dispatchEvent('core.model.before_delete', $entryId, false);
        $this->dispatchEvent(
            static::EVENT_PREFIX . '.model.' . $repository::TABLE_NAME . '.after_delete', $entryId, false
        );

        return $affectedRows;
    }
}
