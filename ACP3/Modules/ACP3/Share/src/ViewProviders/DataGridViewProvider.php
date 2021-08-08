<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\ViewProviders;

use ACP3\Core\DataGrid\ColumnRenderer\IntegerColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\ReplaceValueColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\RoundNumberColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\RouteColumnRenderer;
use ACP3\Core\DataGrid\DataGrid;
use ACP3\Core\DataGrid\Input;
use ACP3\Core\Helpers\ResultsPerPage;
use ACP3\Core\I18n\Translator;
use ACP3\Modules\ACP3\Share\Repository\DataGridRepository;
use ACP3\Modules\ACP3\System\Installer\Schema;

class DataGridViewProvider
{
    /**
     * @var \ACP3\Core\DataGrid\DataGrid
     */
    private $dataGrid;
    /**
     * @var \ACP3\Modules\ACP3\Share\Repository\DataGridRepository
     */
    private $dataGridRepository;
    /**
     * @var \ACP3\Core\Helpers\ResultsPerPage
     */
    private $resultsPerPage;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;

    public function __construct(
        DataGrid $dataGrid,
        DataGridRepository $dataGridRepository,
        ResultsPerPage $resultsPerPage,
        Translator $translator
    ) {
        $this->dataGrid = $dataGrid;
        $this->dataGridRepository = $dataGridRepository;
        $this->resultsPerPage = $resultsPerPage;
        $this->translator = $translator;
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
            ->setIdentifier('#share-data-grid')
            ->setResourcePathDelete('admin/share/index/delete')
            ->setResourcePathEdit('admin/share/index/edit')
            ->addColumn([
                'label' => $this->translator->t('share', 'active'),
                'type' => ReplaceValueColumnRenderer::class,
                'fields' => ['active'],
                'custom' => [
                    'search' => [0, 1],
                    'replace' => [$this->translator->t('system', 'no'), $this->translator->t('system', 'yes')],
                ],
            ], 50)
            ->addColumn([
                'label' => $this->translator->t('share', 'uri'),
                'type' => RouteColumnRenderer::class,
                'fields' => ['uri'],
                'default_sort' => true,
                'custom' => [
                    'path' => '%s',
                ],
            ], 40)
            ->addColumn([
                'label' => $this->translator->t('share', 'average_rating'),
                'type' => RoundNumberColumnRenderer::class,
                'fields' => ['average_rating'],
                'custom' => [
                    'precision' => 2,
                ],
            ], 30)
            ->addColumn([
                'label' => $this->translator->t('share', 'ratings_count'),
                'type' => IntegerColumnRenderer::class,
                'fields' => ['ratings_count'],
            ], 20)
            ->addColumn([
                'label' => $this->translator->t('system', 'id'),
                'type' => IntegerColumnRenderer::class,
                'fields' => ['id'],
                'primary' => true,
            ], 10);
    }
}
