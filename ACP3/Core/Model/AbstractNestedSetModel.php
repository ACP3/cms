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
     * @param AbstractRepository $repository
     * @param Insert $insertOperation
     * @param Edit $editOperation
     * @param $deleteOperation
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        AbstractRepository $repository,
        Insert $insertOperation,
        Edit $editOperation,
        Delete $deleteOperation
    ) {
        parent::__construct($eventDispatcher, $repository);

        $this->insertOperation = $insertOperation;
        $this->editOperation = $editOperation;
        $this->deleteOperation = $deleteOperation;
    }

    /**
     * @param array $data
     * @param int|null $entryId
     * @return bool|int
     */
    protected function save(array $data, $entryId = null)
    {
        $this->dispatchBeforeSaveEvent($this->repository, $data, $entryId);

        if ($entryId === null) {
            $result = $this->insertOperation->execute($data['parent_id'], $data);

            if ($result !== false) {
                $entryId = $result;
            }
        } else {
            $result = $this->editOperation->execute($entryId, $entryId === 1 ? '' : $data['parent_id'], 0, $data);
        }

        $this->dispatchAfterSaveEvent($this->repository, $data, $entryId);

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
