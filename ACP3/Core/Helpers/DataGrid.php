<?php
namespace ACP3\Core\Helpers;

use ACP3\Core\ACL;
use ACP3\Core\Helpers\DataGrid\ColumnPriorityQueue;
use ACP3\Core\Helpers\DataGrid\ColumnRenderer\ColumnRendererInterface;
use ACP3\Core\Lang;
use ACP3\Core\View;

/**
 * Class DataGrid
 * @package ACP3\Core\Helpers
 */
class DataGrid
{
    /**
     * @var \ACP3\Core\ACL
     */
    protected $acl;
    /**
     * @var \ACP3\Core\Lang
     */
    protected $lang;
    /**
     * @var \ACP3\Core\View
     */
    protected $view;
    /**
     * @var array
     */
    protected $results;
    /**
     * @var string
     */
    protected $resourcePathEdit;
    /**
     * @var string
     */
    protected $resourcePathDelete;
    /**
     * @var string
     */
    protected $identifier;
    /**
     * @var int
     */
    protected $recordsPerPage;
    /**
     * @var \ACP3\Core\Helpers\DataGrid\ColumnPriorityQueue
     */
    protected $columns;
    /**
     * @var \ACP3\Core\Helpers\DataGrid\ColumnRenderer\ColumnRendererInterface[]
     */
    protected $columnRenderer = [];

    /**
     * @param \ACP3\Core\ACL  $acl
     * @param \ACP3\Core\Lang $lang
     * @param \ACP3\Core\View $view
     */
    public function __construct(
        ACL $acl,
        Lang $lang,
        View $view
    )
    {
        $this->acl = $acl;
        $this->lang = $lang;
        $this->view = $view;
        $this->columns = new ColumnPriorityQueue();
    }

    /**
     * @param \ACP3\Core\Helpers\DataGrid\ColumnRenderer\ColumnRendererInterface $columnRenderer
     *
     * @return $this
     */
    public function registerColumnRenderer(ColumnRendererInterface $columnRenderer)
    {
        $this->columnRenderer[$columnRenderer->getType()] = $columnRenderer;

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
        $this->recordsPerPage = (int)$recordsPerPage;

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
                'primary' => false
            ],
            $columnData
        );

        $this->columns->insert($columnData, $priority);

        return $this;
    }

    /**
     * @return string
     */
    public function generateDataTable()
    {
        $canDelete = $this->acl->hasPermission($this->resourcePathDelete);
        $canEdit = $this->acl->hasPermission($this->resourcePathEdit);

        if ($canDelete) {
            $this->addColumn([
                'label' => $this->identifier,
                'type' => 'mass_delete',
                'class' => 'datagrid-column datagrid-column__mass-delete',
                'sortable' => false,
                'custom' => [
                    'can_delete' => $canDelete
                ]
            ], 1000);
        }

        $this->addColumn([
            'label' => $this->lang->t('system', 'action'),
            'type' => 'options',
            'class' => 'datagrid-column datagrid-column__actions',
            'sortable' => false,
            'custom' => [
                'can_delete' => $canDelete,
                'can_edit' => $canEdit,
                'resource_path_delete' => $this->resourcePathDelete,
                'resource_path_edit' => $this->resourcePathEdit
            ]
        ], 0);

        $dataTable = [
            'can_edit' => $canEdit,
            'can_delete' => $canDelete,
            'header' => $this->renderTableHeader(),
            'config' => $this->generateDataTableConfig(),
            'results' => $this->mapTableColumnsToDbFields()
        ];
        $this->view->assign('dataTable', $dataTable);

        return $this->view->fetchTemplate('system/datagrid.tpl');
    }

    /**
     * @return string
     */
    protected function renderTableHeader()
    {
        $header = '';

        foreach (clone $this->columns as $column) {
            $header .= $this->columnRenderer['table_header']->fetchDataAndRenderColumn($column, []);
        }

        return $header;
    }


    /**
     * @return string
     */
    protected function mapTableColumnsToDbFields()
    {
        $results = '';
        foreach ($this->results as $result) {
            $results .= "<tr>\n";
            foreach (clone $this->columns as $column) {
                if (array_key_exists($column['type'], $this->columnRenderer)) {
                    $results .= $this->columnRenderer[$column['type']]->fetchDataAndRenderColumn($column, $result);
                }
            }

            $results .= "</tr>\n";
        }

        return $results;
    }

    /**
     * @return array
     */
    protected function generateDataTableConfig()
    {
        $columnDefinitions = [];
        $i = 0;

        $defaultSortColumn = $defaultSortDirection = null;
        foreach (clone $this->columns as $column) {
            if ($column['sortable'] === false) {
                $columnDefinitions[] = $i;
            };

            if ($column['default_sort'] === true &&
                in_array($column['default_sort_direction'], ['asc', 'desc'])
            ) {
                $defaultSortColumn = $i;
                $defaultSortDirection = $column['default_sort_direction'];
            }

            ++$i;
        }

        return [
            'element' => $this->identifier,
            'records_per_page' => $this->recordsPerPage,
            'hide_col_sort' => implode(', ', $columnDefinitions),
            'sort_col' => $defaultSortColumn,
            'sort_dir' => $defaultSortDirection
        ];
    }
}