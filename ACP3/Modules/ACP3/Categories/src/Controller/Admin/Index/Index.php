<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Categories\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Helpers\ResultsPerPage;
use ACP3\Modules\ACP3\Categories;
use ACP3\Modules\ACP3\System\Installer\Schema;

class Index extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Categories\Model\Repository\DataGridRepository
     */
    protected $dataGridRepository;
    /**
     * @var \ACP3\Core\DataGrid\DataGrid
     */
    private $dataGrid;
    /**
     * @var \ACP3\Core\ACL
     */
    private $acl;
    /**
     * @var \ACP3\Core\Helpers\ResultsPerPage
     */
    private $resultsPerPage;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\ACL $acl,
        ResultsPerPage $resultsPerPage,
        Categories\Model\Repository\DataGridRepository $dataGridRepository,
        Core\DataGrid\DataGrid $dataGrid
    ) {
        parent::__construct($context);

        $this->dataGridRepository = $dataGridRepository;
        $this->dataGrid = $dataGrid;
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
            ->setRecordsPerPage($this->resultsPerPage->getResultsPerPage(Schema::MODULE_NAME))
            ->setIdentifier('#categories-data-grid')
            ->setResourcePathDelete('admin/categories/index/delete')
            ->setResourcePathEdit('admin/categories/index/edit');

        $this->addDataGridColumns($input);

        return $this->dataGrid->render($input);
    }

    /**
     * @param \ACP3\Core\DataGrid\Input $input
     */
    protected function addDataGridColumns(Core\DataGrid\Input $input)
    {
        $input
            ->addColumn([
                'label' => $this->translator->t('categories', 'title'),
                'type' => Core\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['title_nested'],
                'sortable' => false,
            ], 50)
            ->addColumn([
                'label' => $this->translator->t('system', 'description'),
                'type' => Core\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['description'],
                'sortable' => false,
            ], 40)
            ->addColumn([
                'label' => $this->translator->t('categories', 'module'),
                'type' => Core\DataGrid\ColumnRenderer\TranslateColumnRenderer::class,
                'fields' => ['module'],
                'sortable' => false,
            ], 30)
            ->addColumn([
                'label' => $this->translator->t('system', 'id'),
                'type' => Core\DataGrid\ColumnRenderer\IntegerColumnRenderer::class,
                'fields' => ['id'],
                'primary' => true,
                'sortable' => false,
            ], 10);

        if ($this->acl->hasPermission('admin/categories/index/order')) {
            $input
                ->addColumn([
                    'label' => $this->translator->t('system', 'order'),
                    'type' => Core\DataGrid\ColumnRenderer\NestedSetSortColumnRenderer::class,
                    'fields' => ['left_id'],
                    'sortable' => false,
                    'custom' => [
                        'route_sort_down' => 'acp/categories/index/order/id_%d/action_down',
                        'route_sort_up' => 'acp/categories/index/order/id_%d/action_up',
                    ],
                ], 20);
        }
    }
}
