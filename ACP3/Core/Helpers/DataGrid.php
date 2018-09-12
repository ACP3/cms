<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers;

use ACP3\Core\DataGrid\Input;
use ACP3\Core\Helpers\DataGrid\ColumnPriorityQueue;
use ACP3\Core\Model\Repository\DataGridRepository;

/**
 * @deprecated Since version 4.30.0, to be removed in 5.0.0. Use class ACP3\Core\DataGrid\DataGrid instead
 */
class DataGrid
{
    /**
     * @var \ACP3\Core\DataGrid\DataGrid
     */
    private $dataGrid;
    /**
     * @var \ACP3\Core\Model\Repository\DataGridRepository
     */
    protected $repository;
    /**
     * @var array
     */
    protected $results = [];
    /**
     * @var string|null
     */
    protected $resourcePathEdit;
    /**
     * @var string|null
     */
    protected $resourcePathDelete;
    /**
     * @var string|null
     */
    protected $identifier;
    /**
     * @var int
     */
    protected $recordsPerPage = 10;
    /**
     * @var bool
     */
    protected $enableMassAction = true;
    /**
     * @var bool
     */
    protected $enableOptions = true;
    /**
     * @var \ACP3\Core\Helpers\DataGrid\ColumnPriorityQueue
     */
    protected $columns;

    /**
     * @param \ACP3\Core\DataGrid\DataGrid $dataGrid
     */
    public function __construct(\ACP3\Core\DataGrid\DataGrid $dataGrid)
    {
        $this->dataGrid = $dataGrid;
        $this->columns = new ColumnPriorityQueue();
    }

    /**
     * @param \ACP3\Core\Model\Repository\DataGridRepository $repository
     *
     * @return $this
     */
    public function setRepository(DataGridRepository $repository)
    {
        $this->repository = $repository;

        return $this;
    }

    /**
     * @param array $results
     *
     * @return $this
     */
    public function setResults($results)
    {
        $this->results = $results;

        return $this;
    }

    /**
     * @param string $resourcePathEdit
     *
     * @return $this
     */
    public function setResourcePathEdit($resourcePathEdit)
    {
        $this->resourcePathEdit = $resourcePathEdit;

        return $this;
    }

    /**
     * @param string $resourcePathDelete
     *
     * @return $this
     */
    public function setResourcePathDelete($resourcePathDelete)
    {
        $this->resourcePathDelete = $resourcePathDelete;

        return $this;
    }

    /**
     * @param int $recordsPerPage
     *
     * @return $this
     */
    public function setRecordsPerPage($recordsPerPage)
    {
        $this->recordsPerPage = (int) $recordsPerPage;

        return $this;
    }

    /**
     * @param bool $enableMassAction
     *
     * @return $this
     */
    public function setEnableMassAction($enableMassAction)
    {
        $this->enableMassAction = (bool) $enableMassAction;

        return $this;
    }

    /**
     * @param bool $enableOptions
     *
     * @return $this
     */
    public function setEnableOptions($enableOptions)
    {
        $this->enableOptions = (bool) $enableOptions;

        return $this;
    }

    /**
     * @param string $identifier
     *
     * @return $this
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * @param array $columnData
     * @param int   $priority
     *
     * @return $this
     */
    public function addColumn(array $columnData, $priority)
    {
        $columnData = \array_merge(
            [
                'label' => '',
                'type' => '',
                'fields' => [],
                'class' => '',
                'style' => '',
                'sortable' => true,
                'default_sort' => false,
                'default_sort_direction' => 'asc',
                'custom' => [],
                'attribute' => [],
                'primary' => false,
            ],
            $columnData
        );

        $this->columns->insert($columnData, $priority);

        return $this;
    }

    /**
     * @return array
     */
    public function render()
    {
        $input = (new Input())
            ->setEnableMassAction($this->enableMassAction)
            ->setEnableOptions($this->enableOptions)
            ->setRepository($this->repository)
            ->setRecordsPerPage($this->recordsPerPage)
            ->setResourcePathDelete($this->resourcePathDelete)
            ->setResourcePathEdit($this->resourcePathEdit)
            ->setIdentifier($this->identifier);

        if ($this->repository === null) {
            $input->setResults($this->results);
        }

        foreach (clone $this->columns as $index => $column) {
            $input->addColumn($column, $index * 10);
        }

        return $this->dataGrid->render($input);
    }

    /**
     * @return int
     */
    public function countDbResults()
    {
        return \count($this->fetchDbResults());
    }

    /**
     * @return array
     */
    protected function fetchDbResults()
    {
        if (empty($this->results) && $this->repository instanceof DataGridRepository) {
            $this->results = $this->repository->getAll(clone $this->columns);
        }

        return $this->results;
    }
}
