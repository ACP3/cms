<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model;

use ACP3\Core\Helpers\Sort;
use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Core\Model\Repository\AbstractRepository;

trait SortingAwareTrait
{
    /**
     * @return AbstractRepository
     */
    abstract protected function getRepository();

    abstract protected function getSortHelper(): Sort;

    /**
     * Return the field name of the DB table's primary key.
     */
    abstract protected function getPrimaryKeyField(): string;

    /**
     * Returns the field name which holds the order of the results.
     */
    abstract protected function getSortingField(): string;

    /**
     * Returns an additional field name by which the order of the results should be constrained.
     */
    protected function getSortingConstraint(): string
    {
        return '';
    }

    public function moveUp(int $id): void
    {
        $this->move('up', $id);
    }

    public function moveDown(int $id): void
    {
        $this->move('down', $id);
    }

    private function move(string $action, int $id): void
    {
        $this->dispatchBeforeSaveEvent($this->getRepository(), $this->createModelSaveEvent(
            $id,
            false,
            true
        ));

        if ($action === 'up') {
            $this->getSortHelper()->up(
                $this->getRepository()::TABLE_NAME,
                $this->getPrimaryKeyField(),
                $this->getSortingField(),
                $id,
                $this->getSortingConstraint()
            );
        } else {
            $this->getSortHelper()->down(
                $this->getRepository()::TABLE_NAME,
                $this->getPrimaryKeyField(),
                $this->getSortingField(),
                $id,
                $this->getSortingConstraint()
            );
        }

        $this->dispatchAfterSaveEvent($this->getRepository(), $this->createModelSaveEvent(
            $id,
            false,
            true
        ));
    }

    abstract protected function dispatchBeforeSaveEvent(AbstractRepository $repository, ModelSaveEvent $event);

    abstract protected function dispatchAfterSaveEvent(AbstractRepository $repository, ModelSaveEvent $event);

    abstract protected function createModelSaveEvent(
        $entryId,
        bool $isNewEntry,
        bool $hasDataChanges,
        array $filteredData = [],
        array $rawData = []
    ): ModelSaveEvent;
}
