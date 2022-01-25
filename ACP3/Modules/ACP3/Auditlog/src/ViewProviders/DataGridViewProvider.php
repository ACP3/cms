<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Auditlog\ViewProviders;

use ACP3\Core\DataGrid\ColumnRenderer\IntegerColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\TranslateColumnRenderer;
use ACP3\Core\DataGrid\DataGrid;
use ACP3\Core\DataGrid\Input;
use ACP3\Core\Helpers\ResultsPerPage;
use ACP3\Core\I18n\Translator;
use ACP3\Modules\ACP3\Auditlog\Repository\AuditLogDataGridRepository;
use ACP3\Modules\ACP3\System\Installer\Schema;
use Symfony\Component\HttpFoundation\JsonResponse;

class DataGridViewProvider
{
    public function __construct(private DataGrid $dataGrid, private AuditLogDataGridRepository $dataGridRepository, private ResultsPerPage $resultsPerPage, private Translator $translator)
    {
    }

    /**
     * @return array<string, array<string, mixed>>|JsonResponse
     */
    public function __invoke(): array|JsonResponse
    {
        return $this->dataGrid->render($this->configureDataGrid());
    }

    protected function configureDataGrid(): Input
    {
        return (new Input())
            ->setUseAjax(true)
            ->setRepository($this->dataGridRepository)
            ->setRecordsPerPage($this->resultsPerPage->getResultsPerPage(Schema::MODULE_NAME))
            ->setIdentifier('#auditlog-data-grid')
            ->setResourcePathEdit('admin/auditlog/index/table/table_%s')
            ->setPrimaryKey('table_name')
            ->addColumn([
                'label' => $this->translator->t('auditlog', 'database_table'),
                'type' => TranslateColumnRenderer::class,
                'fields' => ['module_name', 'table_name'],
                'class' => 'w-100',
                'default_sort' => true,
            ], 20)
            ->addColumn([
                'label' => $this->translator->t('auditlog', 'results_count'),
                'type' => IntegerColumnRenderer::class,
                'fields' => ['results_count'],
            ], 10);
    }
}
