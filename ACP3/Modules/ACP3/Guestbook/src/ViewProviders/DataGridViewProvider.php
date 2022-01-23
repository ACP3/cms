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
