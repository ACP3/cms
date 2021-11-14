<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Auditlog\ViewProviders;

use ACP3\Core\DataGrid\ColumnRenderer\IntegerColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\TextColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\TranslateColumnRenderer;
use ACP3\Core\DataGrid\DataGrid;
use ACP3\Core\DataGrid\Input;
use ACP3\Core\DataGrid\QueryOption;
use ACP3\Core\Helpers\ResultsPerPage;
use ACP3\Core\I18n\Translator;
use ACP3\Modules\ACP3\Auditlog\Repository\AuditLogByTableDataGridRepository;
use ACP3\Modules\ACP3\System\Installer\Schema;

class DataGridByTableViewProvider
{
    public function __construct(private DataGrid $dataGrid, private AuditLogByTableDataGridRepository $dataGridRepository, private ResultsPerPage $resultsPerPage, private Translator $translator)
    {
    }

    public function __invoke(string $tableName)
    {
        return $this->dataGrid->render($this->configureDataGrid($tableName));
    }

    protected function configureDataGrid(string $tableName): Input
    {
        return (new Input())
            ->setUseAjax(true)
            ->setRepository($this->dataGridRepository)
            ->setRecordsPerPage($this->resultsPerPage->getResultsPerPage(Schema::MODULE_NAME))
            ->setIdentifier('#auditlog-by-table-data-grid')
            ->setQueryOptions(new QueryOption('table_name', $tableName))
            ->setEnableOptions(false)
            ->addColumn([
                'label' => $this->translator->t('auditlog', 'entry_id'),
                'type' => TextColumnRenderer::class,
                'fields' => ['entry_id'],
                'primary' => true,
                'default_sort' => true,
                'default_sort_direction' => 'desc',
            ], 30)
            ->addColumn([
                'label' => $this->translator->t('auditlog', 'versions_count'),
                'type' => IntegerColumnRenderer::class,
                'fields' => ['versions_count'],
            ], 20)
            ->addColumn([
                'label' => $this->translator->t('auditlog', 'last_action'),
                'type' => TranslateColumnRenderer::class,
                'fields' => ['last_action'],
                'class' => 'w-100',
                'custom' => [
                    'domain' => 'auditlog',
                ],
            ], 10);
    }
}
