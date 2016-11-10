<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Seo;
use ACP3\Modules\ACP3\System\Installer\Schema;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Seo\Controller\Admin\Index
 */
class Index extends Core\Controller\AbstractAdminAction
{
    /**
     * @var \ACP3\Modules\ACP3\Seo\Model\Repository\DataGridRepository
     */
    protected $dataGridRepository;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Helper\MetaStatements
     */
    protected $metaStatements;

    /**
     * Index constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext $context
     * @param \ACP3\Modules\ACP3\Seo\Model\Repository\DataGridRepository $dataGridRepository
     * @param \ACP3\Modules\ACP3\Seo\Helper\MetaStatements $metaStatements
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Seo\Model\Repository\DataGridRepository $dataGridRepository,
        Seo\Helper\MetaStatements $metaStatements
    ) {
        parent::__construct($context);

        $this->dataGridRepository = $dataGridRepository;
        $this->metaStatements = $metaStatements;
    }

    /**
     * @return array
     */
    public function execute()
    {
        /** @var Core\Helpers\DataGrid $dataGrid */
        $dataGrid = $this->get('core.helpers.data_grid');
        $dataGrid
            ->setRepository($this->dataGridRepository)
            ->setRecordsPerPage($this->resultsPerPage->getResultsPerPage(Schema::MODULE_NAME))
            ->setIdentifier('#acp-table')
            ->setResourcePathDelete('admin/seo/index/delete')
            ->setResourcePathEdit('admin/seo/index/edit');

        $this->addDataGridColumns($dataGrid);

        return [
            'grid' => $dataGrid->render(),
            'show_mass_delete_button' => $dataGrid->countDbResults() > 0
        ];
    }

    /**
     * @param Core\Helpers\DataGrid $dataGrid
     */
    protected function addDataGridColumns(Core\Helpers\DataGrid $dataGrid)
    {
        $dataGrid
            ->addColumn([
                'label' => $this->translator->t('seo', 'uri'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['uri'],
                'default_sort' => true
            ], 60)
            ->addColumn([
                'label' => $this->translator->t('seo', 'alias'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['alias'],
            ], 50)
            ->addColumn([
                'label' => $this->translator->t('seo', 'keywords'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['keywords'],
            ], 40)
            ->addColumn([
                'label' => $this->translator->t('seo', 'description'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['description'],
            ], 30)
            ->addColumn([
                'label' => $this->translator->t('seo', 'robots'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\ReplaceValueColumnRenderer::class,
                'fields' => ['robots'],
                'custom' => [
                    'search' => [0, 1, 2, 3, 4],
                    'replace' => [
                        $this->translator->t(
                            'seo',
                            'robots_use_system_default',
                            ['%default%' => $this->metaStatements->getRobotsSetting()]
                        ),
                        $this->translator->t('seo', 'robots_index_follow'),
                        $this->translator->t('seo', 'robots_index_nofollow'),
                        $this->translator->t('seo', 'robots_noindex_follow'),
                        $this->translator->t('seo', 'robots_noindex_nofollow')
                    ]
                ]
            ], 20)
            ->addColumn([
                'label' => $this->translator->t('system', 'id'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\IntegerColumnRenderer::class,
                'fields' => ['id'],
                'primary' => true
            ], 10);
    }
}
