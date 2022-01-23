<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\ViewProviders;

use ACP3\Core\ACL;
use ACP3\Core\DataGrid\ColumnRenderer\IntegerColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\NestedSetSortColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\ReplaceValueColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\TextColumnRenderer;
use ACP3\Core\DataGrid\DataGrid;
use ACP3\Core\DataGrid\Input;
use ACP3\Core\DataGrid\QueryOption;
use ACP3\Core\Helpers\ResultsPerPage;
use ACP3\Core\I18n\Translator;
use ACP3\Modules\ACP3\Menus\Repository\MenuItemDataGridRepository;
use ACP3\Modules\ACP3\System\Installer\Schema;
use Symfony\Component\HttpFoundation\JsonResponse;

class MenuItemsDataGridViewProvider
{
    public function __construct(private ACL $acl, private DataGrid $dataGrid, private MenuItemDataGridRepository $dataGridRepository, private ResultsPerPage $resultsPerPage, private Translator $translator)
    {
    }

    /**
     * @return array<string, mixed>|JsonResponse
     */
    public function __invoke(int $menuId): array|JsonResponse
    {
        return $this->dataGrid->render($this->configureDataGrid($menuId));
    }

    private function configureDataGrid(int $menuId): Input
    {
        $input = (new Input())
            ->setUseAjax(true)
            ->setRepository($this->dataGridRepository)
            ->setIdentifier("#menu-items-{$menuId}-data-grid")
            ->setRecordsPerPage($this->resultsPerPage->getResultsPerPage(Schema::MODULE_NAME))
            ->setResourcePathEdit('admin/menus/items/edit')
            ->setResourcePathDelete('admin/menus/items/delete')
            ->setQueryOptions(
                new QueryOption(
                    'block_id',
                    (string) $menuId,
                    'r'
                )
            )
            ->addColumn([
                'label' => $this->translator->t('menus', 'title'),
                'type' => TextColumnRenderer::class,
                'fields' => ['title_nested'],
                'sortable' => false,
                'class' => 'w-100',
            ], 40)
            ->addColumn([
                'label' => $this->translator->t('menus', 'page_type'),
                'type' => ReplaceValueColumnRenderer::class,
                'fields' => ['mode'],
                'sortable' => false,
                'custom' => [
                    'search' => ['1', '2', '3'],
                    'replace' => [
                        $this->translator->t('menus', 'module'),
                        $this->translator->t('menus', 'dynamic_page'),
                        $this->translator->t('menus', 'hyperlink'),
                    ],
                ],
            ], 30)
            ->addColumn([
                'label' => $this->translator->t('system', 'id'),
                'type' => IntegerColumnRenderer::class,
                'fields' => ['id'],
                'primary' => true,
                'sortable' => false,
            ], 10);

        if ($this->acl->hasPermission('admin/menus/items/order')) {
            $input
                ->addColumn([
                    'label' => $this->translator->t('system', 'order'),
                    'type' => NestedSetSortColumnRenderer::class,
                    'fields' => ['left_id'],
                    'sortable' => false,
                    'class' => 'text-center',
                    'custom' => [
                        'route_sort_down' => 'acp/menus/items/order/id_%d/action_down',
                        'route_sort_up' => 'acp/menus/items/order/id_%d/action_up',
                    ],
                ], 20);
        }

        return $input;
    }
}
