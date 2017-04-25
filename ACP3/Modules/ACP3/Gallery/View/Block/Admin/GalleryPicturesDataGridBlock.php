<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Gallery\View\Block\Admin;

use ACP3\Core;
use ACP3\Core\Helpers\DataGrid;
use ACP3\Core\View\Block\AbstractDataGridBlock;
use ACP3\Core\View\Block\Context;
use ACP3\Modules\ACP3\Gallery\Installer\Schema;

class GalleryPicturesDataGridBlock extends AbstractDataGridBlock
{
    /**
     * @var Core\ACL
     */
    private $acl;

    /**
     * GalleryPicturesDataGridBlock constructor.
     * @param Context\DataGridBlockContext $context
     * @param Core\ACL $acl
     */
    public function __construct(Context\DataGridBlockContext $context, Core\ACL $acl)
    {
        parent::__construct($context);

        $this->acl = $acl;
    }

    /**
     * @inheritdoc
     */
    protected function addDataGridColumns(DataGrid $dataGrid)
    {
        $dataGrid
            ->addColumn([
                'label' => $this->translator->t('gallery', 'picture'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\PictureColumnRenderer::class,
                'fields' => ['id'],
                'custom' => [
                    'pattern' => 'gallery/index/image/id_%s/action_thumb',
                    'isRoute' => true
                ]
            ], 40)
            ->addColumn([
                'label' => $this->translator->t('system', 'description'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['description'],
            ], 30)
            ->addColumn([
                'label' => $this->translator->t('system', 'id'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\IntegerColumnRenderer::class,
                'fields' => ['id'],
                'primary' => true
            ], 10);

        if ($this->acl->hasPermission('admin/gallery/pictures/order')) {
            $dataGrid
                ->addColumn([
                    'label' => $this->translator->t('system', 'order'),
                    'type' => Core\Helpers\DataGrid\ColumnRenderer\SortColumnRenderer::class,
                    'fields' => ['pic'],
                    'default_sort' => true,
                    'custom' => [
                        'route_sort_down' => 'acp/gallery/pictures/order/id_%d/action_down',
                        'route_sort_up' => 'acp/gallery/pictures/order/id_%d/action_up',
                    ]
                ], 20);
        }
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $data = $this->getData();

        $dataGrid = $this->getCurrentDataGrid();
        $this->configureDataGrid($dataGrid, [
            'identifier' => '#gallery-edit-data-grid',
            'resource_path_delete' => 'admin/gallery/pictures/delete/id_' . $data['galleryId'],
            'resource_path_edit' => 'admin/gallery/pictures/edit'
        ]);

        return [
            'grid' => $dataGrid->render(),
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