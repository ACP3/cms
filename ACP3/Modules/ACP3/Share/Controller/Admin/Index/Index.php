<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Share;
use ACP3\Modules\ACP3\System\Installer\Schema;

class Index extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Share\Model\Repository\DataGridRepository
     */
    protected $dataGridRepository;
    /**
     * @var \ACP3\Core\DataGrid\DataGrid
     */
    private $dataGrid;

    /**
     * Index constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext                $context
     * @param \ACP3\Modules\ACP3\Share\Model\Repository\DataGridRepository $dataGridRepository
     * @param \ACP3\Core\DataGrid\DataGrid                                 $dataGrid
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Share\Model\Repository\DataGridRepository $dataGridRepository,
        Core\DataGrid\DataGrid $dataGrid
    ) {
        parent::__construct($context);

        $this->dataGridRepository = $dataGridRepository;
        $this->dataGrid = $dataGrid;
    }

    /**
     * @return array
     */
    public function execute()
    {
        $input = (new Core\DataGrid\Input())
            ->setRepository($this->dataGridRepository)
            ->setRecordsPerPage($this->resultsPerPage->getResultsPerPage(Schema::MODULE_NAME))
            ->setIdentifier('#share-data-grid')
            ->setResourcePathDelete('admin/share/index/delete')
            ->setResourcePathEdit('admin/share/index/edit');

        $this->addDataGridColumns($input);

        return [
            'grid' => $this->dataGrid->render($input),
            'show_mass_delete_button' => $input->getResultsCount() > 0,
        ];
    }

    /**
     * @param \ACP3\Core\DataGrid\Input $input
     */
    protected function addDataGridColumns(Core\DataGrid\Input $input)
    {
        $input
            ->addColumn([
                'label' => $this->translator->t('share', 'active'),
                'type' => Core\DataGrid\ColumnRenderer\ReplaceValueColumnRenderer::class,
                'fields' => ['active'],
                'custom' => [
                    'search' => [0, 1],
                    'replace' => [$this->translator->t('system', 'no'), $this->translator->t('system', 'yes')],
                ],
            ], 50)
            ->addColumn([
                'label' => $this->translator->t('share', 'uri'),
                'type' => Core\DataGrid\ColumnRenderer\RouteColumnRenderer::class,
                'fields' => ['uri'],
                'default_sort' => true,
                'custom' => [
                    'path' => '%s',
                ],
            ], 40)
            ->addColumn([
                'label' => $this->translator->t('share', 'average_rating'),
                'type' => Core\DataGrid\ColumnRenderer\RoundNumberColumnRenderer::class,
                'fields' => ['average_rating'],
                'custom' => [
                    'precision' => 2,
                ],
            ], 30)
            ->addColumn([
                'label' => $this->translator->t('share', 'ratings_count'),
                'type' => Core\DataGrid\ColumnRenderer\IntegerColumnRenderer::class,
                'fields' => ['ratings_count'],
            ], 20)
            ->addColumn([
                'label' => $this->translator->t('system', 'id'),
                'type' => Core\DataGrid\ColumnRenderer\IntegerColumnRenderer::class,
                'fields' => ['id'],
                'primary' => true,
            ], 10);
    }
}
