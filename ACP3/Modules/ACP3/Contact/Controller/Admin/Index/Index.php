<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Contact\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Contact;
use ACP3\Modules\ACP3\System\Installer\Schema;

class Index extends Core\Controller\AbstractAdminAction
{
    /**
     * @var Contact\Model\Repository\DataGridRepository
     */
    private $dataGridRepository;

    /**
     * Index constructor.
     * @param Core\Controller\Context\FrontendContext $context
     * @param Contact\Model\Repository\DataGridRepository $dataGridRepository
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Contact\Model\Repository\DataGridRepository $dataGridRepository
    ) {
        parent::__construct($context);

        $this->dataGridRepository = $dataGridRepository;
    }

    public function execute()
    {
        /** @var Core\Helpers\DataGrid $dataGrid */
        $dataGrid = $this->get('core.helpers.data_grid');
        $dataGrid
            ->setRepository($this->dataGridRepository)
            ->setRecordsPerPage($this->resultsPerPage->getResultsPerPage(Schema::MODULE_NAME))
            ->setIdentifier('#acp-table')
            ->setEnableOptions(false);

        $this->addDataGridColumns($dataGrid);

        return [
            'grid' => $dataGrid->render()
        ];
    }

    /**
     * @param Core\Helpers\DataGrid $dataGrid
     */
    protected function addDataGridColumns(Core\Helpers\DataGrid $dataGrid)
    {
        $dataGrid
            ->addColumn([
                'label' => $this->translator->t('system', 'date'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\DateColumnRenderer::class,
                'fields' => ['date'],
                'default_sort' => true,
                'default_sort_direction' => 'desc'
            ], 40)
            ->addColumn([
                'label' => $this->translator->t('system', 'name'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['name'],
            ], 30)
            ->addColumn([
                'label' => $this->translator->t('system', 'email_address'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['mail'],
            ], 20)
            ->addColumn([
                'label' => $this->translator->t('system', 'message'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\Nl2pColumnRenderer::class,
                'fields' => ['message'],
            ], 10);
    }
}
