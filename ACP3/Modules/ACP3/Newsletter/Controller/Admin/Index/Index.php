<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Newsletter;
use ACP3\Modules\ACP3\System\Installer\Schema;

class Index extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Model\Repository\NewsletterDataGridRepository
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

    /**
     * Index constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext                               $context
     * @param \ACP3\Core\Helpers\ResultsPerPage                                           $resultsPerPage
     * @param \ACP3\Modules\ACP3\Newsletter\Model\Repository\NewsletterDataGridRepository $dataGridRepository
     * @param \ACP3\Core\DataGrid\DataGrid                                                $dataGrid
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Helpers\ResultsPerPage $resultsPerPage,
        Newsletter\Model\Repository\NewsletterDataGridRepository $dataGridRepository,
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
            ->setIdentifier('#newsletter-data-grid')
            ->setResourcePathEdit('admin/newsletter/index/edit')
            ->setResourcePathDelete('admin/newsletter/index/delete');

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
                'label' => $this->translator->t('newsletter', 'subject'),
                'type' => Core\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['title'],
                'class' => 'datagrid-column__max-width',
            ], 40)
            ->addColumn([
                'label' => $this->translator->t('newsletter', 'status'),
                'type' => Core\DataGrid\ColumnRenderer\ReplaceValueColumnRenderer::class,
                'fields' => ['status'],
                'custom' => [
                    'search' => [0, 1],
                    'replace' => [
                        $this->translator->t('newsletter', 'not_yet_sent'),
                        $this->translator->t('newsletter', 'already_sent'),
                    ],
                ],
            ], 30)
            ->addColumn([
                'label' => $this->translator->t('system', 'id'),
                'type' => Core\DataGrid\ColumnRenderer\IntegerColumnRenderer::class,
                'fields' => ['id'],
                'primary' => true,
                'class' => 'text-right',
            ], 10);
    }
}
