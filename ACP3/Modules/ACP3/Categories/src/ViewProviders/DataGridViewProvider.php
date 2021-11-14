<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Categories\ViewProviders;

use ACP3\Core\ACL;
use ACP3\Core\DataGrid\ColumnRenderer\IntegerColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\NestedSetSortColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\TextColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\TranslateColumnRenderer;
use ACP3\Core\DataGrid\DataGrid;
use ACP3\Core\DataGrid\Input;
use ACP3\Core\Helpers\ResultsPerPage;
use ACP3\Core\I18n\Translator;
use ACP3\Modules\ACP3\Categories\Repository\DataGridRepository;
use ACP3\Modules\ACP3\System\Installer\Schema;

class DataGridViewProvider
{
    public function __construct(private ACL $acl, private DataGrid $dataGrid, private DataGridRepository $dataGridRepository, private ResultsPerPage $resultsPerPage, private Translator $translator)
    {
    }

    /**
     * @return array|array[]|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function __invoke(): array|\Symfony\Component\HttpFoundation\JsonResponse
    {
        return $this->dataGrid->render($this->configureDataGrid());
    }

    private function configureDataGrid(): Input
    {
        $input = (new Input())
            ->setUseAjax(true)
            ->setRepository($this->dataGridRepository)
            ->setRecordsPerPage($this->resultsPerPage->getResultsPerPage(Schema::MODULE_NAME))
            ->setIdentifier('#categories-data-grid')
            ->setResourcePathDelete('admin/categories/index/delete')
            ->setResourcePathEdit('admin/categories/index/edit')
            ->addColumn([
                'label' => $this->translator->t('categories', 'title'),
                'type' => TextColumnRenderer::class,
                'fields' => ['title_nested'],
                'class' => 'w-100',
                'sortable' => false,
            ], 50)
            ->addColumn([
                'label' => $this->translator->t('categories', 'module'),
                'type' => TranslateColumnRenderer::class,
                'fields' => ['module'],
                'sortable' => false,
            ], 30)
            ->addColumn([
                'label' => $this->translator->t('system', 'id'),
                'type' => IntegerColumnRenderer::class,
                'fields' => ['id'],
                'primary' => true,
                'sortable' => false,
            ], 10);

        if ($this->acl->hasPermission('admin/categories/index/order')) {
            $input
                ->addColumn([
                    'label' => $this->translator->t('system', 'order'),
                    'type' => NestedSetSortColumnRenderer::class,
                    'fields' => ['left_id'],
                    'class' => 'text-center',
                    'sortable' => false,
                    'custom' => [
                        'route_sort_down' => 'acp/categories/index/order/id_%d/action_down',
                        'route_sort_up' => 'acp/categories/index/order/id_%d/action_up',
                    ],
                ], 20);
        }

        return $input;
    }
}
