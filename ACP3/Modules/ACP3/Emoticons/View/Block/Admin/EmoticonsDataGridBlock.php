<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\View\Block\Admin;

use ACP3\Core;
use ACP3\Core\Helpers\DataGrid;
use ACP3\Core\View\Block\AbstractDataGridBlock;
use ACP3\Core\View\Block\Context;
use ACP3\Modules\ACP3\Emoticons\Installer\Schema;
use Symfony\Component\HttpFoundation\JsonResponse;

class EmoticonsDataGridBlock extends AbstractDataGridBlock
{
    /**
     * @var Core\Environment\ApplicationPath
     */
    private $appPath;

    /**
     * EmoticonsDataGridBlock constructor.
     * @param Context\DataGridBlockContext $context
     * @param Core\Environment\ApplicationPath $appPath
     */
    public function __construct(Context\DataGridBlockContext $context, Core\Environment\ApplicationPath $appPath)
    {
        parent::__construct($context);

        $this->appPath = $appPath;
    }

    /**
     * @inheritdoc
     */
    protected function addDataGridColumns(DataGrid $dataGrid)
    {
        $dataGrid
            ->addColumn([
                'label' => $this->translator->t('system', 'description'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['description'],
                'default_sort' => true,
            ], 40)
            ->addColumn([
                'label' => $this->translator->t('emoticons', 'code'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['code'],
            ], 30)
            ->addColumn([
                'label' => $this->translator->t('emoticons', 'picture'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\PictureColumnRenderer::class,
                'fields' => ['img'],
                'custom' => [
                    'pattern' => $this->appPath->getWebRoot() . 'uploads/emoticons/%s',
                ],
            ], 20)
            ->addColumn([
                'label' => $this->translator->t('system', 'id'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\IntegerColumnRenderer::class,
                'fields' => ['id'],
                'primary' => true,
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
            'identifier' => '#emoticons-data-grid',
            'resource_path_delete' => 'admin/emoticons/index/delete',
            'resource_path_edit' => 'admin/emoticons/index/manage',
        ]);

        $grid = $dataGrid->render();
        if ($grid instanceof JsonResponse) {
            return $grid;
        }

        return [
            'grid' => $grid,
            'show_mass_delete_button' => $dataGrid->countDbResults() > 0,
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
