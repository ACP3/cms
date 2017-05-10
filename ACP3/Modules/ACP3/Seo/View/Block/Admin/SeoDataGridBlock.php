<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\View\Block\Admin;

use ACP3\Core;
use ACP3\Core\Helpers\DataGrid;
use ACP3\Core\View\Block\AbstractDataGridBlock;
use ACP3\Core\View\Block\Context;
use ACP3\Modules\ACP3\Seo\Helper\MetaStatements;
use ACP3\Modules\ACP3\Seo\Installer\Schema;
use Symfony\Component\HttpFoundation\JsonResponse;

class SeoDataGridBlock extends AbstractDataGridBlock
{
    /**
     * @var MetaStatements
     */
    private $metaStatements;

    /**
     * SeoDataGridBlock constructor.
     * @param Context\DataGridBlockContext $context
     * @param MetaStatements $metaStatements
     */
    public function __construct(Context\DataGridBlockContext $context, MetaStatements $metaStatements)
    {
        parent::__construct($context);

        $this->metaStatements = $metaStatements;
    }

    /**
     * @inheritdoc
     */
    protected function addDataGridColumns(DataGrid $dataGrid)
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

    /**
     * @inheritdoc
     */
    public function render()
    {
        $dataGrid = $this->getCurrentDataGrid();
        $this->configureDataGrid($dataGrid, [
            'ajax' => true,
            'identifier' => '#seo-data-grid',
            'resource_path_delete' => 'admin/seo/index/delete',
            'resource_path_edit' => 'admin/seo/index/edit',
        ]);

        $grid = $dataGrid->render();
        if ($grid instanceof JsonResponse) {
            return $grid;
        }

        return [
            'grid' => $grid,
            'show_mass_delete_button' => $dataGrid->countDbResults() > 0
        ];
    }

    /**
     * @inheritdoc
     */
    public function getModuleName(): string
    {
        return Schema::MODULE_NAME;
    }
}
