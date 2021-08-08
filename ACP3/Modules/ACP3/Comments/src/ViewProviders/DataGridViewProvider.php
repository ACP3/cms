<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Comments\ViewProviders;

use ACP3\Core\DataGrid\ColumnRenderer\IntegerColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\TranslateColumnRenderer;
use ACP3\Core\DataGrid\DataGrid;
use ACP3\Core\DataGrid\Input;
use ACP3\Core\Helpers\ResultsPerPage;
use ACP3\Core\I18n\Translator;
use ACP3\Modules\ACP3\Comments\Repository\CommentsByModuleDataGridRepository;
use ACP3\Modules\ACP3\System\Installer\Schema;

class DataGridViewProvider
{
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;
    /**
     * @var \ACP3\Core\DataGrid\DataGrid
     */
    private $dataGrid;
    /**
     * @var \ACP3\Modules\ACP3\Comments\Repository\CommentsByModuleDataGridRepository
     */
    private $dataGridRepository;
    /**
     * @var \ACP3\Core\Helpers\ResultsPerPage
     */
    private $resultsPerPage;

    public function __construct(
        DataGrid $dataGrid,
        CommentsByModuleDataGridRepository $dataGridRepository,
        ResultsPerPage $resultsPerPage,
        Translator $translator
    ) {
        $this->translator = $translator;
        $this->dataGrid = $dataGrid;
        $this->dataGridRepository = $dataGridRepository;
        $this->resultsPerPage = $resultsPerPage;
    }

    /**
     * @return array|array[]|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function __invoke()
    {
        return $this->dataGrid->render($this->configureDataGrid());
    }

    private function configureDataGrid(): Input
    {
        return (new Input())
            ->setUseAjax(true)
            ->setRepository($this->dataGridRepository)
            ->setRecordsPerPage($this->resultsPerPage->getResultsPerPage(Schema::MODULE_NAME))
            ->setIdentifier('#comments-data-grid')
            ->setResourcePathDelete('admin/comments/index/delete')
            ->setResourcePathEdit('admin/comments/details/index')
            ->addColumn([
                'label' => $this->translator->t('comments', 'module'),
                'type' => TranslateColumnRenderer::class,
                'fields' => ['module'],
                'default_sort' => true,
            ], 30)
            ->addColumn([
                'label' => $this->translator->t('comments', 'comments_count'),
                'type' => IntegerColumnRenderer::class,
                'fields' => ['comments_count'],
            ], 20)
            ->addColumn([
                'label' => $this->translator->t('system', 'id'),
                'type' => IntegerColumnRenderer::class,
                'fields' => ['module_id'],
                'primary' => true,
            ], 10);
    }
}
