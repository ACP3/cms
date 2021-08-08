<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles\ViewProviders;

use ACP3\Core\DataGrid\ColumnRenderer\DateColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\ReplaceValueColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\RouteColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\TextColumnRenderer;
use ACP3\Core\DataGrid\DataGrid;
use ACP3\Core\DataGrid\Input;
use ACP3\Core\Helpers\ResultsPerPage;
use ACP3\Core\I18n\Translator;
use ACP3\Modules\ACP3\Articles\Helpers;
use ACP3\Modules\ACP3\Articles\Repository\DataGridRepository;
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
     * @var \ACP3\Modules\ACP3\Articles\Repository\DataGridRepository
     */
    private $dataGridRepository;
    /**
     * @var \ACP3\Core\Helpers\ResultsPerPage
     */
    private $resultsPerPage;

    public function __construct(
        DataGrid $dataGrid,
        DataGridRepository $dataGridRepository,
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
            ->setIdentifier('#articles-data-grid')
            ->setResourcePathDelete('admin/articles/index/delete')
            ->setResourcePathEdit('admin/articles/index/edit')
            ->addColumn([
                'label' => $this->translator->t('articles', 'active'),
                'type' => ReplaceValueColumnRenderer::class,
                'fields' => ['active'],
                'custom' => [
                    'search' => [0, 1],
                    'replace' => [$this->translator->t('system', 'no'), $this->translator->t('system', 'yes')],
                ],
            ], 40)
            ->addColumn([
                'label' => $this->translator->t('system', 'publication_period'),
                'type' => DateColumnRenderer::class,
                'fields' => ['start', 'end'],
            ], 30)
            ->addColumn([
                'label' => $this->translator->t('articles', 'title'),
                'type' => TextColumnRenderer::class,
                'fields' => ['title'],
                'default_sort' => true,
            ], 20)
            ->addColumn([
                'label' => $this->translator->t('system', 'id'),
                'type' => RouteColumnRenderer::class,
                'fields' => ['id'],
                'primary' => true,
                'custom' => [
                    'path' => Helpers::URL_KEY_PATTERN,
                ],
            ], 10);
    }
}
