<?php
namespace ACP3\Core\Helpers;

use ACP3\Core\ACL;
use ACP3\Core\Helpers\DataTable\ColumnRenderer;
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
     * @var \ACP3\Core\Helpers\DataTable\ColumnRenderer
     */
    protected $columnRenderer;
    /**
     * @var \ACP3\Core\Model
     */
    protected $repository;
    /**
     * @var string
     */
    protected $resourcePathEdit;
    /**
     * @var string
     */
    protected $resourcePathDelete;
    /**
     * @var array
     */
    protected $dataTableConfig = [];
    /**
     * @var \SplPriorityQueue
     */
    protected $columns;

    /**
     * @param \ACP3\Core\ACL                              $acl
     * @param \ACP3\Core\Lang                             $lang
     * @param \ACP3\Core\View                             $view
     * @param \ACP3\Core\Helpers\DataTable\ColumnRenderer $columnRenderer
     */
    public function __construct(
        ACL $acl,
        Lang $lang,
        View $view,
        ColumnRenderer $columnRenderer
    )
    {
        $this->acl = $acl;
        $this->lang = $lang;
        $this->view = $view;
        $this->columnRenderer = $columnRenderer;
        $this->columns = new \SplPriorityQueue();
    }

    /**
     * @param \ACP3\Core\Model $repository
     *
     * @return $this
     */
    public function setRepository(Model $repository)
    {
        $this->repository = $repository;

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
     * @param array $dataTableConfig
     *
     * @return $this
     */
    public function setDataTableConfig(array $dataTableConfig)
    {
        $this->dataTableConfig = $dataTableConfig;

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
        $this->addColumn([
            'label' => $this->lang->t('system', 'action'),
            'type' => 'action_buttons',
            'class' => 'table-cell-actions'
        ], 0);

        $dataTable = [
            'can_edit' => $this->acl->hasPermission($this->resourcePathEdit),
            'can_delete' => $this->acl->hasPermission($this->resourcePathDelete),
            'header' => $this->generateTableHeader($this->columns),
            'config' => $this->dataTableConfig,
            'results' => $this->mapTableColumnsToDbFields(
                $this->columns,
                $this->acl->hasPermission($this->resourcePathDelete),
                $this->acl->hasPermission($this->resourcePathEdit)
            )
        ];
        $this->view->assign('dataTable', $dataTable);

        return $this->view->fetchTemplate('system/grid_table.tpl');
    }

    /**
     * @param \SplPriorityQueue $columns
     *
     * @return string
     */
    protected function generateTableHeader(\SplPriorityQueue $columns)
    {
        $header = '';

        foreach (clone $columns as $column) {
            $class = !empty($column['class']) ? ' class="' . $column['class'] . '"' : '';
            $style = !empty($column['style']) ? ' style="' . $column['style'] . '"' : '';
            $header .= "<th{$class}{$style}>{$column['label']}</th>\n";
        }

        return $header;
    }

    /**
     * @param \SplPriorityQueue $columns
     * @param bool              $canDelete
     * @param bool              $canEdit
     *
     * @return string
     */
    protected function mapTableColumnsToDbFields(\SplPriorityQueue $columns, $canDelete, $canEdit)
    {
        $results = '';
        foreach ($this->repository->getAllInAcp() as $result) {
            $results .= "<tr>\n";
            foreach (clone $columns as $column) {
                $firstField = reset($column['fields']);
                if ($firstField !== false && array_key_exists($firstField, $result)) {
                    if (in_array($column['type'], ['string', 'int'])) {
                        $results .= $this->columnRenderer->renderColumn($result[$firstField]);
                    } else if ($column['type'] === 'date') {
                        $results .= $this->columnRenderer->renderDateColumn($result[$firstField]);
                    } else if ($column['type'] === 'date_range') {
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
                }
            }

            $results .= "</tr>\n";
        }

        return $results;
    }
}