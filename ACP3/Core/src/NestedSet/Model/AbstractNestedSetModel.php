<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\NestedSet\Model;

use ACP3\Core\Model\AbstractModel;
use ACP3\Core\Model\DataProcessor;
use ACP3\Core\Model\SortingAwareInterface;
use ACP3\Core\NestedSet\Operation\Delete;
use ACP3\Core\NestedSet\Operation\Edit;
use ACP3\Core\NestedSet\Operation\Insert;
use ACP3\Core\NestedSet\Operation\Sort;
use ACP3\Core\Repository\AbstractRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @property \ACP3\Core\NestedSet\Repository\NestedSetRepository $repository
 */
abstract class AbstractNestedSetModel extends AbstractModel implements SortingAwareInterface
{
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        DataProcessor $dataProcessor,
        AbstractRepository $repository,
        protected Insert $insertOperation,
        protected Edit $editOperation,
        protected Delete $deleteOperation,
        private Sort $sortOperation
    ) {
        parent::__construct($eventDispatcher, $dataProcessor, $repository);
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
            $result = $this->insertOperation->execute($filteredNewData, $filteredNewData['parent_id'] ?: 0);

            if ($result !== false) {
                $entryId = $result;
            }
        } else {
            $result = $this->editOperation->execute(
                $entryId,
                $filteredNewData['parent_id'],
                $filteredNewData[$this->repository::BLOCK_COLUMN_NAME] ?? 0,
                $filteredNewData
            );
        }

        return [
            $entryId,
            $result,
        ];
    }

    /**
     * {@inheritDoc}
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
            $affectedRows += (int) $this->deleteOperation->execute($item);
        }

        $this->dispatchEvent('core.model.before_delete', $event);
        $this->dispatchEvent(
            static::EVENT_PREFIX . '.model.' . $this->repository::TABLE_NAME . '.after_delete',
            $event
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
