<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\View\Block\Admin;

use ACP3\Core;
use ACP3\Core\Helpers\DataGrid;
use ACP3\Core\View\Block\AbstractDataGridBlock;
use ACP3\Core\View\Block\Context\DataGridBlockContext;
use ACP3\Modules\ACP3\Categories\Installer\Schema;
use Symfony\Component\HttpFoundation\JsonResponse;

class RolesDataGridBlock extends AbstractDataGridBlock
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
                'label' => $this->translator->t('system', 'name'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['name_nested'],
                'sortable' => false,
            ], 30)
            ->addColumn([
                'label' => $this->translator->t('system', 'id'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\IntegerColumnRenderer::class,
                'fields' => ['id'],
                'primary' => true,
                'sortable' => false,
            ], 10);

        if ($this->acl->hasPermission('admin/permissions/index/order')) {
            $dataGrid
                ->addColumn([
                    'label' => $this->translator->t('system', 'order'),
                    'type' => Core\Helpers\DataGrid\ColumnRenderer\NestedSetSortColumnRenderer::class,
                    'fields' => ['left_id'],
                    'sortable' => false,
                    'custom' => [
                        'route_sort_down' => 'acp/permissions/index/order/id_%d/action_down',
                        'route_sort_up' => 'acp/permissions/index/order/id_%d/action_up',
                    ],
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
            'identifier' => '#roles-data-grid',
            'resource_path_delete' => 'admin/permissions/index/delete',
            'resource_path_edit' => 'admin/permissions/index/manage',
        ]);

        $grid = $dataGrid->render();
        if ($grid instanceof JsonResponse) {
            return $grid;
        }

        return [
            'grid' => $grid,
            'show_mass_delete_button' => $dataGrid->countDbResults() > 0,
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
