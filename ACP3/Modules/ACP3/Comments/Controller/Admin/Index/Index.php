<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Comments\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Comments;
use ACP3\Modules\ACP3\System\Installer\Schema;

class Index extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Core\DataGrid\DataGrid
     */
    private $dataGrid;
    /**
     * @var \ACP3\Modules\ACP3\Comments\Model\Repository\CommentsByModuleDataGridRepository
     */
    private $dataGridRepository;
    /**
     * @var \ACP3\Core\Helpers\ResultsPerPage
     */
    private $resultsPerPage;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Helpers\ResultsPerPage $resultsPerPage,
        Comments\Model\Repository\CommentsByModuleDataGridRepository $dataGridRepository,
        Core\DataGrid\DataGrid $dataGrid
    ) {
        parent::__construct($context);

        $this->dataGrid = $dataGrid;
        $this->dataGridRepository = $dataGridRepository;
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
            ->setIdentifier('#comments-data-grid')
            ->setResourcePathDelete('admin/comments/index/delete')
            ->setResourcePathEdit('admin/comments/details/index');

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
                'label' => $this->translator->t('comments', 'module'),
                'type' => Core\DataGrid\ColumnRenderer\TranslateColumnRenderer::class,
                'fields' => ['module'],
                'default_sort' => true,
                'class' => 'datagrid-column__max-width',
            ], 30)
            ->addColumn([
                'label' => $this->translator->t('comments', 'comments_count'),
                'type' => Core\DataGrid\ColumnRenderer\IntegerColumnRenderer::class,
                'fields' => ['comments_count'],
                'class' => 'text-right',
            ], 20)
            ->addColumn([
                'fields' => ['module_id'],
                'primary' => true,
            ], 10);
    }
}
