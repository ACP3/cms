<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Guestbook\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Helpers\ResultsPerPage;
use ACP3\Modules\ACP3\Guestbook;
use ACP3\Modules\ACP3\System\Installer\Schema;

class Index extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Guestbook\Model\Repository\DataGridRepository
     */
    protected $dataGridRepository;
    /**
     * @var \ACP3\Core\DataGrid\DataGrid
     */
    private $dataGrid;
    /**
     * @var \ACP3\Core\Helpers\ResultsPerPage
     */
    private $resultsPerPage;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        ResultsPerPage $resultsPerPage,
        Guestbook\Model\Repository\DataGridRepository $dataGridRepository,
        Core\DataGrid\DataGrid $dataGrid
    ) {
        parent::__construct($context);

        $this->dataGridRepository = $dataGridRepository;
        $this->dataGrid = $dataGrid;
        $this->resultsPerPage = $resultsPerPage;
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
            ->setIdentifier('#guestbook-data-grid')
            ->setResourcePathDelete('admin/guestbook/index/delete')
            ->setResourcePathEdit('admin/guestbook/index/edit');

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
                'label' => $this->translator->t('system', 'date'),
                'type' => Core\DataGrid\ColumnRenderer\DateColumnRenderer::class,
                'fields' => ['date'],
                'default_sort' => true,
                'default_sort_direction' => 'desc',
            ], 50)
            ->addColumn([
                'label' => $this->translator->t('system', 'name'),
                'type' => Core\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['name'],
            ], 40)
            ->addColumn([
                'label' => $this->translator->t('system', 'message'),
                'type' => Core\DataGrid\ColumnRenderer\Nl2pColumnRenderer::class,
                'fields' => ['message'],
            ], 30)
            ->addColumn([
                'label' => $this->translator->t('guestbook', 'ip'),
                'type' => Core\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['ip'],
            ], 20)
            ->addColumn([
                'label' => $this->translator->t('system', 'id'),
                'type' => Core\DataGrid\ColumnRenderer\IntegerColumnRenderer::class,
                'fields' => ['id'],
                'primary' => true,
            ], 10);
    }
}
