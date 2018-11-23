<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\DataGrid;

use ACP3\Core\DataGrid\Model\Repository\AbstractDataGridRepository;

class Input
{
    /**
     * @var AbstractDataGridRepository
     */
    private $repository;
    /**
     * @var array
     */
    private $results = [];
    /**
     * @var string|null
     */
    private $resourcePathEdit;
    /**
     * @var string|null
     */
    private $resourcePathDelete;
    /**
     * @var string|null
     */
    private $identifier;
    /**
     * @var int
     */
    private $recordsPerPage = 10;
    /**
     * @var bool
     */
    private $useAjax = false;
    /**
     * @var bool
     */
    private $enableMassAction = true;
    /**
     * @var bool
     */
    private $enableOptions = true;
    /**
     * @var \ACP3\Core\DataGrid\ColumnPriorityQueue
     */
    private $columns;
    /**
     * @var string|null
     */
    private $primaryKey;
    /**
     * @var QueryOption[]
     */
    private $queryOptions = [];

    public function __construct()
    {
        $this->columns = new ColumnPriorityQueue();
    }

    /**
     * @param \ACP3\Core\DataGrid\Model\Repository\AbstractDataGridRepository $repository
     *
     * @return $this
     */
    public function setRepository(AbstractDataGridRepository $repository): self
    {
        $this->repository = $repository;

        return $this;
    }

    /**
     * @param array $results
     *
     * @return $this
     */
    public function setResults(array $results): self
    {
        $this->results = $results;

        return $this;
    }

    /**
     * @param string $resourcePathEdit
     *
     * @return $this
     */
    public function setResourcePathEdit(string $resourcePathEdit): self
    {
        $this->resourcePathEdit = $resourcePathEdit;

        return $this;
    }

    /**
     * @param string $resourcePathDelete
     *
     * @return $this
     */
    public function setResourcePathDelete(string $resourcePathDelete): self
    {
        $this->resourcePathDelete = $resourcePathDelete;

        return $this;
    }

    /**
     * @param int $recordsPerPage
     *
     * @return $this
     */
    public function setRecordsPerPage(int $recordsPerPage): self
    {
        $this->recordsPerPage = $recordsPerPage;

        return $this;
    }

    /**
     * @param bool $useAjax
     *
     * @return $this
     */
    public function setUseAjax(bool $useAjax): self
    {
        $this->useAjax = $useAjax;

        return $this;
    }

    /**
     * @param bool $enableMassAction
     *
     * @return $this
     */
    public function setEnableMassAction(bool $enableMassAction): self
    {
        $this->enableMassAction = $enableMassAction;

        return $this;
    }

    /**
     * @param bool $enableOptions
     *
     * @return $this
     */
    public function setEnableOptions(bool $enableOptions): self
    {
        $this->enableOptions = $enableOptions;

        return $this;
    }

    /**
     * @param string $identifier
     *
     * @return $this
     */
    public function setIdentifier(string $identifier): self
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
    public function addColumn(array $columnData, int $priority): self
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
    public function getResults(): array
    {
        if (empty($this->results) && $this->repository instanceof AbstractDataGridRepository) {
            $this->setResults($this->repository->getAll(clone $this->columns, ...$this->queryOptions));
        }

        return $this->results;
    }

    /**
     * @return int
     */
    public function getResultsCount()
    {
        if ($this->repository instanceof AbstractDataGridRepository) {
            return $this->repository->countAll(...$this->queryOptions);
        }

        return \count($this->getResults());
    }

    /**
     * @return null|string
     */
    public function getResourcePathEdit(): ?string
    {
        return $this->resourcePathEdit;
    }

    /**
     * @return null|string
     */
    public function getResourcePathDelete(): ?string
    {
        return $this->resourcePathDelete;
    }

    /**
     * @return null|string
     */
    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    /**
     * @return int
     */
    public function getRecordsPerPage(): int
    {
        return $this->recordsPerPage;
    }

    /**
     * @return bool
     */
    public function isUseAjax(): bool
    {
        return $this->useAjax;
    }

    /**
     * @return bool
     */
    public function isEnableMassAction(): bool
    {
        return $this->enableMassAction;
    }

    /**
     * @return bool
     */
    public function isEnableOptions(): bool
    {
        return $this->enableOptions;
    }

    /**
     * @return \ACP3\Core\DataGrid\ColumnPriorityQueue
     */
    public function getColumns(): ColumnPriorityQueue
    {
        return $this->columns;
    }

    /**
     * @param string $primaryKey
     *
     * @return $this
     */
    public function setPrimaryKey(string $primaryKey): self
    {
        $this->primaryKey = $primaryKey;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPrimaryKey(): ?string
    {
        if ($this->primaryKey === null) {
            foreach (clone $this->getColumns() as $column) {
                if ($column['primary'] === true && !empty($column['fields'])) {
                    $this->primaryKey = \reset($column['fields']);

                    break;
                }
            }
        }

        return $this->primaryKey;
    }

    /**
     * @return \ACP3\Core\DataGrid\QueryOption[]
     */
    public function getQueryOptions(): array
    {
        return $this->queryOptions;
    }

    /**
     * @param \ACP3\Core\DataGrid\QueryOption ...$queryOptions
     *
     * @return \ACP3\Core\DataGrid\Input
     */
    public function setQueryOptions(QueryOption ...$queryOptions): self
    {
        $this->queryOptions = $queryOptions;

        return $this;
    }
}
