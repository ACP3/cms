<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Auditlog\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Controller\Context\FrontendContext;
use ACP3\Core\DataGrid\DataGrid;
use ACP3\Modules\ACP3\Auditlog\Installer\Schema;
use ACP3\Modules\ACP3\Auditlog\Model\Repository\AuditLogDataGridRepository;

class Index extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Core\DataGrid\DataGrid
     */
    private $dataGrid;
    /**
     * @var \ACP3\Modules\ACP3\Auditlog\Model\Repository\AuditLogDataGridRepository
     */
    private $dataGridRepository;

    public function __construct(
        FrontendContext $context,
        DataGrid $dataGrid,
        AuditLogDataGridRepository $dataGridRepository
    ) {
        parent::__construct($context);

        $this->dataGrid = $dataGrid;
        $this->dataGridRepository = $dataGridRepository;
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
            ->setIdentifier('#auditlog-data-grid')
            ->setEnableOptions(false);

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
                'label' => $this->translator->t('system', 'module'),
                'type' => Core\DataGrid\ColumnRenderer\TranslateColumnRenderer::class,
                'fields' => ['module_name'],
            ], 40)
            ->addColumn([
                'label' => $this->translator->t('auditlog', 'results_count'),
                'type' => Core\DataGrid\ColumnRenderer\IntegerColumnRenderer::class,
                'fields' => ['results_count'],
            ], 30);
    }
}
