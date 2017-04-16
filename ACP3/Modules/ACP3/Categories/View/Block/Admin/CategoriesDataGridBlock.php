<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Categories\View\Block\Admin;

use ACP3\Core;
use ACP3\Core\Helpers\DataGrid;
use ACP3\Core\View\Block\AbstractDataGridBlock;
use ACP3\Modules\ACP3\Categories\Installer\Schema;

class CategoriesDataGridBlock extends AbstractDataGridBlock
{
    /**
     * @inheritdoc
     */
    protected function addDataGridColumns(DataGrid $dataGrid)
    {
        $dataGrid
            ->addColumn([
                'label' => $this->translator->t('categories', 'title'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['title'],
                'default_sort' => true
            ], 30)
            ->addColumn([
                'label' => $this->translator->t('system', 'description'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['description']
            ], 20)
            ->addColumn([
                'label' => $this->translator->t('categories', 'module'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TranslateColumnRenderer::class,
                'fields' => ['module'],
            ], 20)
            ->addColumn([
                'label' => $this->translator->t('system', 'id'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\IntegerColumnRenderer::class,
                'fields' => ['id'],
                'primary' => true
            ], 10);
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $dataGrid = $this->getCurrentDataGrid();

        $this->configureDataGrid($dataGrid, [
            'identifier' => '#categories-data-grid',
            'resource_path_delete' => 'admin/categories/index/delete',
            'resource_path_edit' => 'admin/categories/index/edit'
        ]);

        $this->addDataGridColumns($dataGrid);

        return [
            'grid' => $dataGrid->render(),
            'show_mass_delete_button' => $dataGrid->countDbResults() > 0
        ];
    }

    /**
     * @inheritdoc
     */
    public function getModuleName(): string
    {
        return Schema::MODULE_NAME;
    }
}
