<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Contact\ViewProviders;

use ACP3\Core\DataGrid\ColumnRenderer\DateColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\IntegerColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\Nl2pColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\TextColumnRenderer;
use ACP3\Core\DataGrid\DataGrid;
use ACP3\Core\DataGrid\Input;
use ACP3\Core\Helpers\ResultsPerPage;
use ACP3\Core\I18n\Translator;
use ACP3\Modules\ACP3\Contact\Repository\DataGridRepository;
use ACP3\Modules\ACP3\System\Installer\Schema;
use Symfony\Component\HttpFoundation\JsonResponse;

class DataGridViewProvider
{
    public function __construct(private DataGrid $dataGrid, private DataGridRepository $dataGridRepository, private ResultsPerPage $resultsPerPage, private Translator $translator)
    {
    }

    /**
     * @return array<string, array<string, mixed>>|JsonResponse
     */
    public function __invoke(): array|JsonResponse
    {
        return $this->dataGrid->render($this->configureDataGrid());
    }

    private function configureDataGrid(): Input
    {
        return (new Input())
            ->setUseAjax(true)
            ->setRepository($this->dataGridRepository)
            ->setRecordsPerPage($this->resultsPerPage->getResultsPerPage(Schema::MODULE_NAME))
            ->setIdentifier('#contact-data-grid')
            ->setResourcePathDelete('admin/contact/index/delete')
            ->addColumn([
                'label' => $this->translator->t('system', 'date'),
                'type' => DateColumnRenderer::class,
                'fields' => ['date'],
                'default_sort' => true,
                'default_sort_direction' => 'desc',
            ], 40)
            ->addColumn([
                'label' => $this->translator->t('system', 'name'),
                'type' => TextColumnRenderer::class,
                'fields' => ['name'],
            ], 30)
            ->addColumn([
                'label' => $this->translator->t('system', 'email_address'),
                'type' => TextColumnRenderer::class,
                'fields' => ['mail'],
            ], 20)
            ->addColumn([
                'label' => $this->translator->t('system', 'message'),
                'type' => Nl2pColumnRenderer::class,
                'fields' => ['message'],
                'class' => 'w-100',
            ], 10)
            ->addColumn([
                'label' => $this->translator->t('system', 'id'),
                'type' => IntegerColumnRenderer::class,
                'fields' => ['id'],
                'primary' => true,
            ], 5);
    }
}
