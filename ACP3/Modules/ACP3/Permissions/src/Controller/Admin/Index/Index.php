<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Controller\Context\FrontendContext;
use ACP3\Core\Helpers\ResultsPerPage;
use ACP3\Modules\ACP3\Permissions\Installer\Schema;
use ACP3\Modules\ACP3\Permissions\Model\Repository\RolesDataGridRepository;

class Index extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Core\DataGrid\DataGrid
     */
    private $dataGrid;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Model\Repository\RolesDataGridRepository
     */
    private $dataGridRepository;
    /**
     * @var \ACP3\Core\ACL
     */
    private $acl;
    /**
     * @var \ACP3\Core\Helpers\ResultsPerPage
     */
    private $resultsPerPage;

    public function __construct(
        FrontendContext $context,
        Core\ACL $acl,
        Core\DataGrid\DataGrid $dataGrid,
        ResultsPerPage $resultsPerPage,
        RolesDataGridRepository $dataGridRepository
    ) {
        parent::__construct($context);

        $this->dataGrid = $dataGrid;
        $this->dataGridRepository = $dataGridRepository;
        $this->acl = $acl;
        $this->resultsPerPage = $resultsPerPage;
    }

    /**
     * @return array
     */
    public function execute()
    {
        $input = (new Core\DataGrid\Input())
            ->setUseAjax(true)
            ->setRepository($this->dataGridRepository)
            ->setIdentifier('#roles-data-grid')
            ->setRecordsPerPage($this->resultsPerPage->getResultsPerPage(Schema::MODULE_NAME))
            ->setResourcePathEdit('admin/permissions/index/edit')
            ->setResourcePathDelete('admin/permissions/index/delete');

        $this->addDataGridColumns($input);

        return $this->dataGrid->render($input);
    }

    private function addDataGridColumns(Core\DataGrid\Input $input): void
    {
        $input
            ->addColumn([
                'label' => $this->translator->t('system', 'name'),
                'type' => Core\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['name_nested'],
                'sortable' => false,
            ], 30)
            ->addColumn([
                'label' => $this->translator->t('system', 'id'),
                'type' => Core\DataGrid\ColumnRenderer\IntegerColumnRenderer::class,
                'fields' => ['id'],
                'primary' => true,
                'sortable' => false,
            ], 10);

        if ($this->acl->hasPermission('admin/permissions/index/order')) {
            $input
                ->addColumn([
                    'label' => $this->translator->t('system', 'order'),
                    'type' => Core\DataGrid\ColumnRenderer\NestedSetSortColumnRenderer::class,
                    'fields' => ['left_id'],
                    'sortable' => false,
                    'custom' => [
                        'route_sort_down' => 'acp/permissions/index/order/id_%d/action_down',
                        'route_sort_up' => 'acp/permissions/index/order/id_%d/action_up',
                    ],
                ], 20);
        }
    }
}
