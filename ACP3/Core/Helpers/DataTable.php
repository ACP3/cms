<?php
namespace ACP3\Core\Helpers;

use ACP3\Core\ACL;
use ACP3\Core\Helpers\DataTable\ColumnPriorityQueue;
use ACP3\Core\Helpers\DataTable\ColumnRenderer;
use ACP3\Core\Helpers\DataTable\HeaderColumnRenderer;
use ACP3\Core\Lang;
use ACP3\Core\Model;
use ACP3\Core\View;

/**
 * Class DataTable
 * @package ACP3\Core\Helpers
 */
class DataTable
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
     * @var \ACP3\Core\Helpers\DataTable\HeaderColumnRenderer
     */
    protected $headerColumnRenderer;
    /**
     * @var \ACP3\Core\Helpers\DataTable\ColumnRenderer
     */
    protected $columnRenderer;
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
     * @var \ACP3\Core\Helpers\DataTable\ColumnPriorityQueue
     */
    protected $columns;

    /**
     * @param \ACP3\Core\ACL                                    $acl
     * @param \ACP3\Core\Lang                                   $lang
     * @param \ACP3\Core\View                                   $view
     * @param \ACP3\Core\Helpers\DataTable\HeaderColumnRenderer $headerColumnRenderer
     * @param \ACP3\Core\Helpers\DataTable\ColumnRenderer       $columnRenderer
     */
    public function __construct(
        ACL $acl,
        Lang $lang,
        View $view,
        HeaderColumnRenderer $headerColumnRenderer,
        ColumnRenderer $columnRenderer
    )
    {
        $this->acl = $acl;
        $this->lang = $lang;
        $this->view = $view;
        $this->headerColumnRenderer = $headerColumnRenderer;
        $this->columnRenderer = $columnRenderer;
        $this->columns = new ColumnPriorityQueue();
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
                'default_sort_direction' => 'asc'
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
                'sortable' => false
            ], 1000);
        }

        $this->addColumn([
            'label' => $this->lang->t('system', 'action'),
            'type' => 'action_buttons',
            'class' => 'datagrid-column datagrid-column__actions',
            'sortable' => false
        ], 0);

        $dataTable = [
            'can_edit' => $canEdit,
            'can_delete' => $canDelete,
            'header' => $this->headerColumnRenderer->renderTableHeader($this->columns),
            'config' => $this->generateDataTableConfig(),
            'results' => $this->mapTableColumnsToDbFields(
                $canDelete,
                $canEdit
            )
        ];
        $this->view->assign('dataTable', $dataTable);

        return $this->view->fetchTemplate('system/datagrid.tpl');
    }

    /**
     * @param bool $canDelete
     * @param bool $canEdit
     *
     * @return string
     */
    protected function mapTableColumnsToDbFields($canDelete, $canEdit)
    {
        $results = '';
        foreach ($this->results as $result) {
            $results .= "<tr>\n";
            foreach (clone $this->columns as $column) {
                $firstField = reset($column['fields']);
                if ($firstField !== false && array_key_exists($firstField, $result)) {
                    if (in_array($column['type'], ['string', 'int'])) {
                        $results .= $this->columnRenderer->renderColumn($result[$firstField]);
                    } elseif ($column['type'] === 'date') {
                        $results .= $this->columnRenderer->renderDateColumn($result[$firstField]);
                    } elseif ($column['type'] === 'date_range') {
                        $dateStart = $column['fields'][0];
                        $dateEnd = $column['fields'][1];
                        $results .= $this->columnRenderer->renderDateRangeColumn($result[$dateStart], $result[$dateEnd]);
                    }
                } elseif ($column['type'] === 'action_buttons') {
                    $results .= $this->columnRenderer->renderActionButtons(
                        $result['id'],
                        $this->resourcePathDelete,
                        $this->resourcePathEdit,
                        $canDelete,
                        $canEdit
                    );
                } elseif ($column['type'] === 'mass_delete') {
                    $results .= $this->columnRenderer->renderDeleteCheckbox(
                        $result['id'],
                        $canDelete
                    );
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