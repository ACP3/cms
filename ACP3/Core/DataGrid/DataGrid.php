<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\DataGrid;

use ACP3\Core\ACL;
use ACP3\Core\DataGrid\ColumnRenderer\ColumnRendererInterface;
use ACP3\Core\DataGrid\ColumnRenderer\HeaderColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\MassActionColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\OptionColumnRenderer;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Model\Repository\DataGridRepository;

class DataGrid
{
    /**
     * @var \ACP3\Core\ACL
     */
    protected $acl;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    protected $translator;
    /**
     * @var \ACP3\Core\DataGrid\ColumnRenderer\AbstractColumnRenderer[]
     */
    protected $columnRenderer = [];

    /**
     * @param \ACP3\Core\ACL             $acl
     * @param \ACP3\Core\I18n\Translator $translator
     */
    public function __construct(
        ACL $acl,
        Translator $translator
    ) {
        $this->acl = $acl;
        $this->translator = $translator;
    }

    /**
     * @param \ACP3\Core\DataGrid\ColumnRenderer\ColumnRendererInterface $columnRenderer
     *
     * @return $this
     */
    public function registerColumnRenderer(ColumnRendererInterface $columnRenderer)
    {
        $this->columnRenderer[\get_class($columnRenderer)] = $columnRenderer;

        return $this;
    }

    /**
     * @param \ACP3\Core\DataGrid\Input $input
     *
     * @return array
     */
    public function render(Input $input)
    {
        $canDelete = $this->acl->hasPermission($input->getResourcePathDelete());
        $canEdit = $this->acl->hasPermission($input->getResourcePathEdit());

        $this->addDefaultColumns($input, $canDelete, $canEdit);

        return [
            'can_edit' => $canEdit,
            'can_delete' => $canDelete,
            'identifier' => \substr($input->getIdentifier(), 1),
            'header' => $this->renderTableHeader($input),
            'config' => $this->generateDataTableConfig($input),
            'results' => $this->mapTableColumnsToDbFields($input),
        ];
    }

    /**
     * @param \ACP3\Core\DataGrid\Input $input
     *
     * @return string
     */
    protected function renderTableHeader(Input $input)
    {
        $header = '';
        foreach (clone $input->getColumns() as $column) {
            if (!empty($column['label'])) {
                $header .= $this->columnRenderer[HeaderColumnRenderer::class]
                    ->setIdentifier($input->getIdentifier())
                    ->setPrimaryKey($input->getPrimaryKey())
                    ->fetchDataAndRenderColumn($column, []);
            }
        }

        return $header;
    }

    /**
     * @param \ACP3\Core\DataGrid\Input $input
     *
     * @return string
     */
    protected function mapTableColumnsToDbFields(Input $input)
    {
        $renderedResults = '';
        foreach ($this->fetchDbResults($input) as $result) {
            $renderedResults .= '<tr>';
            foreach (clone $input->getColumns() as $column) {
                if (\array_key_exists($column['type'], $this->columnRenderer) && !empty($column['label'])) {
                    $renderedResults .= $this->columnRenderer[$column['type']]
                        ->setIdentifier($input->getIdentifier())
                        ->setPrimaryKey($input->getPrimaryKey())
                        ->fetchDataAndRenderColumn($column, $result);
                }
            }

            $renderedResults .= "</tr>\n";
        }

        return $renderedResults;
    }

    /**
     * @param \ACP3\Core\DataGrid\Input $input
     *
     * @return array
     */
    protected function generateDataTableConfig(Input $input)
    {
        $columnDefinitions = [];
        $i = 0;

        $defaultSortColumn = $defaultSortDirection = null;
        foreach (clone $input->getColumns() as $column) {
            if ($column['sortable'] === false) {
                $columnDefinitions[] = $i;
            }

            if ($column['default_sort'] === true &&
                \in_array($column['default_sort_direction'], ['asc', 'desc'])
            ) {
                $defaultSortColumn = $i;
                $defaultSortDirection = $column['default_sort_direction'];
            }

            if (!empty($column['label'])) {
                ++$i;
            }
        }

        return [
            'element' => $input->getIdentifier(),
            'records_per_page' => $input->getRecordsPerPage(),
            'hide_col_sort' => \implode(', ', $columnDefinitions),
            'sort_col' => $defaultSortColumn,
            'sort_dir' => $defaultSortDirection,
        ];
    }

    /**
     * @param \ACP3\Core\DataGrid\Input $input
     * @param bool                      $canDelete
     * @param bool                      $canEdit
     */
    protected function addDefaultColumns(Input $input, bool $canDelete, bool $canEdit)
    {
        if ($input->isEnableMassAction() && $canDelete) {
            $input->addColumn([
                'label' => $input->getIdentifier(),
                'type' => MassActionColumnRenderer::class,
                'class' => 'datagrid-column datagrid-column__mass-action',
                'sortable' => false,
                'custom' => [
                    'can_delete' => $canDelete,
                ],
            ], 1000);
        }

        if ($input->isEnableOptions()) {
            $input->addColumn([
                'label' => $this->translator->t('system', 'action'),
                'type' => OptionColumnRenderer::class,
                'class' => 'datagrid-column datagrid-column__actions',
                'sortable' => false,
                'custom' => [
                    'can_delete' => $canDelete,
                    'can_edit' => $canEdit,
                    'resource_path_delete' => $input->getResourcePathDelete(),
                    'resource_path_edit' => $input->getResourcePathEdit(),
                ],
            ], 0);
        }
    }

    /**
     * @param \ACP3\Core\DataGrid\Input $input
     *
     * @return array
     */
    protected function fetchDbResults(Input $input)
    {
        if (empty($input->getResults()) && $input->getRepository() instanceof DataGridRepository) {
            $input->setResults($input->getRepository()->getAll(clone $input->getColumns()));
        }

        return $input->getResults();
    }

    /**
     * @param \ACP3\Core\DataGrid\Input $input
     *
     * @return int
     */
    public function countDbResults(Input $input)
    {
        return \count($this->fetchDbResults($input));
    }
}
