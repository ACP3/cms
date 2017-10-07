<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Categories\View\Block\Admin;

use ACP3\Core;
use ACP3\Core\Helpers\DataGrid;
use ACP3\Core\View\Block\AbstractDataGridBlock;
use ACP3\Core\View\Block\Context\DataGridBlockContext;
use ACP3\Modules\ACP3\Categories\Installer\Schema;
use Symfony\Component\HttpFoundation\JsonResponse;

class CategoriesDataGridBlock extends AbstractDataGridBlock
{
    /**
     * @var Core\ACL\ACLInterface
     */
    private $acl;

    /**
     * CategoriesDataGridBlock constructor.
     * @param DataGridBlockContext $context
     * @param Core\ACL\ACLInterface $acl
     */
    public function __construct(DataGridBlockContext $context, Core\ACL\ACLInterface $acl)
    {
        parent::__construct($context);

        $this->acl = $acl;
    }

    /**
     * @inheritdoc
     */
    protected function addDataGridColumns(DataGrid $dataGrid)
    {
        $dataGrid
            ->addColumn([
                'label' => $this->translator->t('categories', 'title'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['title_nested'],
                'sortable' => false,
            ], 50)
            ->addColumn([
                'label' => $this->translator->t('system', 'description'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['description'],
                'sortable' => false,
            ], 40)
            ->addColumn([
                'label' => $this->translator->t('categories', 'module'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TranslateColumnRenderer::class,
                'fields' => ['module'],
                'sortable' => false,
            ], 30)
            ->addColumn([
                'label' => $this->translator->t('system', 'id'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\IntegerColumnRenderer::class,
                'fields' => ['id'],
                'primary' => true,
                'sortable' => false,
            ], 10);

        if ($this->acl->hasPermission('admin/categories/index/order')) {
            $dataGrid
                ->addColumn([
                    'label' => $this->translator->t('system', 'order'),
                    'type' => Core\Helpers\DataGrid\ColumnRenderer\NestedSetSortColumnRenderer::class,
                    'fields' => ['left_id'],
                    'sortable' => false,
                    'custom' => [
                        'route_sort_down' => 'acp/categories/index/order/id_%d/action_down',
                        'route_sort_up' => 'acp/categories/index/order/id_%d/action_up',
                    ]
                ], 20);
        }
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $dataGrid = $this->getCurrentDataGrid();

        $this->configureDataGrid($dataGrid, [
            'ajax' => true,
            'identifier' => '#categories-data-grid',
            'resource_path_delete' => 'admin/categories/index/delete',
            'resource_path_edit' => 'admin/categories/index/edit'
        ]);

        $grid = $dataGrid->render();
        if ($grid instanceof JsonResponse) {
            return $grid;
        }

        return [
            'grid' => $grid,
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
