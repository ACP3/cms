<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Guestbook\ViewProviders;

use ACP3\Core\DataGrid\ColumnRenderer\DateColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\IntegerColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\Nl2pColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\TextColumnRenderer;
use ACP3\Core\DataGrid\DataGrid;
use ACP3\Core\DataGrid\Input;
use ACP3\Core\Helpers\ResultsPerPage;
use ACP3\Core\I18n\Translator;
use ACP3\Modules\ACP3\Guestbook\Repository\DataGridRepository;
use ACP3\Modules\ACP3\System\Installer\Schema;

class DataGridViewProvider
{
    /**
     * @var \ACP3\Core\DataGrid\DataGrid
     */
    private $dataGrid;
    /**
     * @var \ACP3\Modules\ACP3\Guestbook\Repository\DataGridRepository
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
            ->setIdentifier('#guestbook-data-grid')
            ->setResourcePathDelete('admin/guestbook/index/delete')
            ->setResourcePathEdit('admin/guestbook/index/edit')
            ->addColumn([
                'label' => $this->translator->t('system', 'date'),
                'type' => DateColumnRenderer::class,
                'fields' => ['date'],
                'default_sort' => true,
                'default_sort_direction' => 'desc',
            ], 50)
            ->addColumn([
                'label' => $this->translator->t('system', 'name'),
                'type' => TextColumnRenderer::class,
                'fields' => ['name'],
            ], 40)
            ->addColumn([
                'label' => $this->translator->t('system', 'message'),
                'type' => Nl2pColumnRenderer::class,
                'fields' => ['message'],
                'class' => 'w-100',
            ], 30)
            ->addColumn([
                'label' => $this->translator->t('guestbook', 'ip'),
                'type' => TextColumnRenderer::class,
                'fields' => ['ip'],
            ], 20)
            ->addColumn([
                'label' => $this->translator->t('system', 'id'),
                'type' => IntegerColumnRenderer::class,
                'fields' => ['id'],
                'primary' => true,
            ], 10);
    }
}
