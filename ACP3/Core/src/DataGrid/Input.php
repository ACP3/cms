<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\DataGrid;

use ACP3\Core\DataGrid\Repository\AbstractDataGridRepository;
use Doctrine\DBAL\Exception as DBALException;

final class Input
{
    private ?AbstractDataGridRepository $repository = null;
    /**
     * @var array<array<string, mixed>>
     */
    private array $results = [];

    private string $resourcePathEdit = '';

    private string $resourcePathDelete = '';

    private ?string $identifier = null;

    private int $recordsPerPage = 10;

    private bool $useAjax = false;

    private bool $enableMassAction = true;

    private bool $enableOptions = true;

    private readonly ColumnPriorityQueue $columns;

    private ?string $primaryKey = null;
    /**
     * @var QueryOption[]
     */
    private array $queryOptions = [];

    public function __construct()
    {
        $this->columns = new ColumnPriorityQueue();
    }

    public function setRepository(AbstractDataGridRepository $repository): self
    {
        $this->repository = $repository;

        return $this;
    }

    /**
     * @param array<array<string, mixed>> $results
     */
    public function setResults(array $results): self
    {
        $this->results = $results;

        return $this;
    }

    public function setResourcePathEdit(?string $resourcePathEdit): self
    {
        $this->resourcePathEdit = $resourcePathEdit;

        return $this;
    }

    public function setResourcePathDelete(?string $resourcePathDelete): self
    {
        $this->resourcePathDelete = $resourcePathDelete;

        return $this;
    }

    public function setRecordsPerPage(int $recordsPerPage): self
    {
        $this->recordsPerPage = $recordsPerPage;

        return $this;
    }

    public function setUseAjax(bool $useAjax): self
    {
        $this->useAjax = $useAjax;

        return $this;
    }

    public function setEnableMassAction(bool $enableMassAction): self
    {
        $this->enableMassAction = $enableMassAction;

        return $this;
    }

    public function setEnableOptions(bool $enableOptions): self
    {
        $this->enableOptions = $enableOptions;

        return $this;
    }

    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * @param array<string, mixed> $columnData
     */
    public function addColumn(array $columnData, int $priority): self
    {
        $columnData = array_merge(
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
     * @return array<array<string, mixed>>
     *
     * @throws DBALException
     */
    public function getResults(): array
    {
        if (empty($this->results) && $this->repository instanceof AbstractDataGridRepository) {
            $this->setResults($this->repository->getAll(clone $this->columns, ...$this->queryOptions));
        }

        return $this->results;
    }

    /**
     * @throws DBALException
     */
    public function getResultsCount(): int
    {
        if ($this->repository instanceof AbstractDataGridRepository) {
            return $this->repository->countAll(...$this->queryOptions);
        }

        return \count($this->getResults());
    }

    public function getResourcePathEdit(): string
    {
        return $this->resourcePathEdit;
    }

    public function getResourcePathDelete(): string
    {
        return $this->resourcePathDelete;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function getRecordsPerPage(): int
    {
        return $this->recordsPerPage;
    }

    public function isUseAjax(): bool
    {
        return $this->useAjax;
    }

    public function isEnableMassAction(): bool
    {
        return $this->enableMassAction;
    }

    public function isEnableOptions(): bool
    {
        return $this->enableOptions;
    }

    public function getColumns(): ColumnPriorityQueue
    {
        return $this->columns;
    }

    public function setPrimaryKey(string $primaryKey): self
    {
        $this->primaryKey = $primaryKey;

        return $this;
    }

    public function getPrimaryKey(): ?string
    {
        if ($this->primaryKey === null) {
            foreach (clone $this->getColumns() as $column) {
                if ($column['primary'] === true && !empty($column['fields'])) {
                    $this->primaryKey = reset($column['fields']);

                    break;
                }
            }
        }

        return $this->primaryKey;
    }

    /**
     * @return QueryOption[]
     */
    public function getQueryOptions(): array
    {
        return $this->queryOptions;
    }

    public function setQueryOptions(QueryOption ...$queryOptions): self
    {
        $this->queryOptions = $queryOptions;

        return $this;
    }
}
