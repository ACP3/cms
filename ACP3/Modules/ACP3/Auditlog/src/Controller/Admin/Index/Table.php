<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Auditlog\Controller\Admin\Index;

use ACP3\Core\Controller\AbstractFrontendAction;
use ACP3\Core\Controller\Context\FrontendContext;
use ACP3\Core\DataGrid\ColumnRenderer\IntegerColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\TextColumnRenderer;
use ACP3\Core\DataGrid\ColumnRenderer\TranslateColumnRenderer;
use ACP3\Core\DataGrid\DataGrid;
use ACP3\Core\DataGrid\Input;
use ACP3\Core\DataGrid\QueryOption;
use ACP3\Core\Helpers\ResultsPerPage;
use ACP3\Modules\ACP3\Auditlog\Installer\Schema;
use ACP3\Modules\ACP3\Auditlog\Model\Repository\AuditLogByTableDataGridRepository;

class Table extends AbstractFrontendAction
{
    /**
     * @var \ACP3\Core\DataGrid\DataGrid
     */
    private $dataGrid;
    /**
     * @var \ACP3\Modules\ACP3\Auditlog\Model\Repository\AuditLogByTableDataGridRepository
     */
    private $dataGridRepository;
    /**
     * @var \ACP3\Core\Helpers\ResultsPerPage
     */
    private $resultsPerPage;

    public function __construct(
        FrontendContext $context,
        ResultsPerPage $resultsPerPage,
        DataGrid $dataGrid,
        AuditLogByTableDataGridRepository $dataGridRepository)
    {
        parent::__construct($context);

        $this->dataGrid = $dataGrid;
        $this->dataGridRepository = $dataGridRepository;
        $this->resultsPerPage = $resultsPerPage;
    }

    public function execute(string $table)
    {
        $input = (new Input())
            ->setUseAjax(true)
            ->setRepository($this->dataGridRepository)
            ->setRecordsPerPage($this->resultsPerPage->getResultsPerPage(Schema::MODULE_NAME))
            ->setIdentifier('#auditlog-data-grid')
            ->setEnableOptions(false)
            ->setQueryOptions(new QueryOption('table_name', $table));

        $this->addDataGridColumns($input);

        return $this->dataGrid->render($input);
    }

    protected function addDataGridColumns(Input $input)
    {
        $input
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
                'custom' => [
                    'domain' => 'auditlog',
                ],
            ], 10);
    }
}
