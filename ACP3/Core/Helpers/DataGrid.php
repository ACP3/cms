<?php

namespace ACP3\Core\Helpers;

use ACP3\Core\ACL;
use ACP3\Core\Helpers\DataGrid\ColumnPriorityQueue;
use ACP3\Core\Helpers\DataGrid\ColumnRenderer\ColumnRendererInterface;
use ACP3\Core\Helpers\DataGrid\ColumnRenderer\HeaderColumnRenderer;
use ACP3\Core\Helpers\DataGrid\ColumnRenderer\MassActionColumnRenderer;
use ACP3\Core\Helpers\DataGrid\ColumnRenderer\OptionColumnRenderer;
use ACP3\Core\Helpers\DataGrid\ConfigProcessor;
use ACP3\Core\Helpers\DataGrid\Exception\DataGridException;
use ACP3\Core\Helpers\DataGrid\Model\Repository\AbstractDataGridRepository;
use ACP3\Core\Helpers\DataGrid\Options;
use ACP3\Core\Helpers\DataGrid\QueryOption;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use Symfony\Component\HttpFoundation\JsonResponse;

class DataGrid
{
    /**
     * @var \ACP3\Core\ACL
     */
    private $acl;
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;
    /**
     * @var ConfigProcessor
     */
    private $configProcessor;
    /**
     * @var \ACP3\Core\Helpers\DataGrid\Model\Repository\AbstractDataGridRepository
     */
    private $repository;
    /**
     * @var array
     */
    private $results = [];
    /**
     * @var \ACP3\Core\Helpers\DataGrid\ColumnPriorityQueue
     */
    private $columns;
    /**
     * @var \ACP3\Core\Helpers\DataGrid\ColumnRenderer\ColumnRendererInterface[]
     */
    private $columnRenderer = [];
    /**
     * @var string
     */
    private $primaryKey = '';
    /**
     * @var array|JsonResponse
     */
    private $renderedDataGrid;
    /**
     * @var QueryOption[]
     */
    private $queryOptions = [];
    /**
     * @var Options|null
     */
    private $options;

    /**
     * @param \ACP3\Core\ACL $acl
     * @param RequestInterface $request
     * @param \ACP3\Core\I18n\Translator $translator
     * @param ConfigProcessor $configProcessor
     */
    public function __construct(
        ACL $acl,
        RequestInterface $request,
        Translator $translator,
        ConfigProcessor $configProcessor
    ) {
        $this->acl = $acl;
        $this->translator = $translator;
        $this->columns = new ColumnPriorityQueue();
        $this->request = $request;
        $this->configProcessor = $configProcessor;
    }

    /**
     * @param \ACP3\Core\Helpers\DataGrid\ColumnRenderer\ColumnRendererInterface $columnRenderer
     *
     * @return $this
     */
    public function registerColumnRenderer(ColumnRendererInterface $columnRenderer)
    {
        $this->columnRenderer[get_class($columnRenderer)] = $columnRenderer;

        return $this;
    }

    /**
     * @param \ACP3\Core\Helpers\DataGrid\Model\Repository\AbstractDataGridRepository $repository
     *
     * @return $this
     */
    public function setRepository(AbstractDataGridRepository $repository)
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
     * @param QueryOption[] ...$queryOptions
     * @return $this
     */
    public function setQueryOptions(QueryOption ...$queryOptions)
    {
        $this->queryOptions = $queryOptions;

        return $this;
    }

    /**
     * @param Options $options
     * @return $this
     */
    public function setOptions(Options $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @param array $columnData
     * @param int $priority
     *
     * @return $this
     */
    public function addColumn(array $columnData, $priority)
    {
        $columnData = array_merge(
            [
                'label' => '',
                'type' => '',
                'fields' => [],
                'class' => '',
                'sortable' => true,
                'default_sort' => false,
                'default_sort_direction' => 'asc',
                'custom' => [],
                'attribute' => [],
                'primary' => false
            ],
            $columnData
        );

        $this->columns->insert($columnData, $priority);

        return $this;
    }

    /**
     * @return array|JsonResponse
     * @throws DataGridException
     */
    public function render()
    {
        if ($this->options === null) {
            throw new DataGridException(
                'An error has occurred while rendering the data grid, as no options has been given.' .
                'Please call the method DataGrid::setOptions() first.'
            );
        }

        if ($this->renderedDataGrid === null) {
            $canDelete = $this->acl->hasPermission($this->options->getResourcePathDelete());
            $canEdit = $this->acl->hasPermission($this->options->getResourcePathEdit());

            $this->addDefaultColumns($canDelete, $canEdit);

            if ($this->isRequiredAjaxRequest()) {
                $this->renderedDataGrid = new JsonResponse([
                    'data' => $this->mapTableColumnsToDbFieldsAjax()
                ]);
            } else {
                $this->renderedDataGrid = [
                    'can_edit' => $canEdit,
                    'can_delete' => $canDelete,
                    'identifier' => substr($this->options->getIdentifier(), 1),
                    'header' => $this->renderTableHeader(),
                    'config' => $this->configProcessor->generateDataTableConfig($this->columns, $this->options),
                    'results' => $this->mapTableColumnsToDbFields(),
                    'num_results' => $this->countDbResults()
                ];
            }
        }

        return $this->renderedDataGrid;
    }

    /**
     * Checks, whether we have the required AJAX request in effect
     *
     * @return bool
     */
    private function isRequiredAjaxRequest(): bool
    {
        return $this->request->isXmlHttpRequest()
            && $this->request->getParameters()->get('ajax', '') === substr($this->options->getIdentifier(), 1);
    }

    /**
     * @return string
     */
    private function renderTableHeader(): string
    {
        $header = '';
        foreach (clone $this->columns as $column) {
            if (!empty($column['label'])) {
                $header .= $this->columnRenderer[HeaderColumnRenderer::class]
                    ->setIdentifier($this->options->getIdentifier())
                    ->setPrimaryKey($this->getPrimaryKey())
                    ->fetchDataAndRenderColumn($column, []);
            }
        }

        return $header;
    }

    /**
     * @return string
     */
    private function getPrimaryKey(): string
    {
        if ($this->primaryKey === '') {
            $this->findPrimaryKey();
        }

        return $this->primaryKey;
    }

    /**
     * @return string
     */
    private function mapTableColumnsToDbFields(): string
    {
        $renderedResults = '';
        if (!$this->options->isUseAjax()) {
            foreach ($this->fetchDbResults() as $result) {
                $renderedResults .= '<tr>';
                foreach (clone $this->columns as $column) {
                    if (array_key_exists($column['type'], $this->columnRenderer) && !empty($column['label'])) {
                        $renderedResults .= $this->columnRenderer[$column['type']]
                            ->setIdentifier($this->options->getIdentifier())
                            ->setPrimaryKey($this->getPrimaryKey())
                            ->fetchDataAndRenderColumn($column, $result);
                    }
                }

                $renderedResults .= "</tr>\n";
            }
        }

        return $renderedResults;
    }

    /**
     * @return array
     */
    private function mapTableColumnsToDbFieldsAjax(): array
    {
        $renderedResults = [];
        foreach ($this->fetchDbResults() as $result) {
            $row = [];
            foreach (clone $this->columns as $column) {
                if (array_key_exists($column['type'], $this->columnRenderer) && !empty($column['label'])) {
                    $row[] = $this->columnRenderer[$column['type']]
                        ->setIdentifier($this->options->getIdentifier())
                        ->setPrimaryKey($this->getPrimaryKey())
                        ->setIsAjax($this->isRequiredAjaxRequest())
                        ->fetchDataAndRenderColumn($column, $result);
                }
            }

            $renderedResults[] = $row;
        }

        return $renderedResults;
    }

    /**
     * @param bool $canDelete
     * @param bool $canEdit
     */
    private function addDefaultColumns(bool $canDelete, bool $canEdit)
    {
        if ($this->options->isEnableMassAction() && $canDelete) {
            $this->addColumn([
                'label' => $this->options->getIdentifier(),
                'type' => MassActionColumnRenderer::class,
                'class' => 'datagrid-column datagrid-column__mass-action',
                'sortable' => false,
                'custom' => [
                    'can_delete' => $canDelete
                ]
            ], 1000);
        }

        if ($this->options->isEnableOptions()) {
            $this->addColumn([
                'label' => $this->translator->t('system', 'action'),
                'type' => OptionColumnRenderer::class,
                'class' => 'datagrid-column datagrid-column__actions',
                'sortable' => false,
                'custom' => [
                    'can_delete' => $canDelete,
                    'can_edit' => $canEdit,
                    'resource_path_delete' => $this->options->getResourcePathDelete(),
                    'resource_path_edit' => $this->options->getResourcePathEdit()
                ]
            ], 0);
        }
    }

    /**
     * Finds the primary key column
     */
    private function findPrimaryKey()
    {
        foreach (clone $this->columns as $column) {
            if ($column['primary'] === true && !empty($column['fields'])) {
                $this->primaryKey = reset($column['fields']);
                break;
            }
        }
    }

    /**
     * @return array
     */
    private function fetchDbResults(): array
    {
        if (empty($this->results) && $this->repository instanceof AbstractDataGridRepository) {
            $this->results = $this->repository->getAll(clone $this->columns, ...$this->queryOptions);
        }

        return $this->results;
    }

    /**
     * @return int
     */
    public function countDbResults(): int
    {
        if ($this->repository instanceof AbstractDataGridRepository) {
            return (int)$this->repository->countAll(...$this->queryOptions);
        }

        return count($this->fetchDbResults());
    }
}
