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
use ACP3\Modules\ACP3\Gallery\Helpers;
use ACP3\Modules\ACP3\Gallery\Installer\Schema;
use ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class GalleryPicturesDataGridBlock extends AbstractDataGridBlock
{
    /**
     * @var Core\ACL\ACLInterface
     */
    private $acl;
    /**
     * @var GalleryRepository
     */
    private $galleryRepository;

    /**
     * GalleryPicturesDataGridBlock constructor.
     * @param Context\DataGridBlockContext $context
     * @param Core\ACL\ACLInterface $acl
     * @param GalleryRepository $galleryRepository
     */
    public function __construct(
        Context\DataGridBlockContext $context,
        Core\ACL\ACLInterface $acl,
        GalleryRepository $galleryRepository
    ) {
        parent::__construct($context);

        $this->acl = $acl;
        $this->galleryRepository = $galleryRepository;
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
                    'isRoute' => true,
                ],
            ], 40)
            ->addColumn([
                'label' => $this->translator->t('system', 'description'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['description'],
            ], 30)
            ->addColumn([
                'label' => $this->translator->t('system', 'id'),
                'type' => Core\Helpers\DataGrid\ColumnRenderer\RouteColumnRenderer::class,
                'fields' => ['id'],
                'primary' => true,
                'custom' => [
                    'path' => Helpers::URL_KEY_PATTERN_PICTURE,
                ],
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
                    ],
                ], 20);
        }
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $data = $this->getData();

        $this->addBreadcrumbSteps($data['gallery_id']);

        $dataGrid = $this->getCurrentDataGrid();
        $this->configureDataGrid($dataGrid, [
            'ajax' => true,
            'identifier' => '#gallery-pictures-data-grid',
            'resource_path_delete' => 'admin/gallery/pictures/delete/id_' . $data['gallery_id'],
            'resource_path_edit' => 'admin/gallery/pictures/edit',
            'query_options' => [
                new DataGrid\QueryOption('gallery_id', $data['gallery_id']),
            ],
        ]);

        $grid = $dataGrid->render();
        if ($grid instanceof JsonResponse) {
            return $grid;
        }

        return [
            'gallery_id' => $data['gallery_id'],
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

    /**
     * @param int $galleryId
     */
    private function addBreadcrumbSteps(int $galleryId): void
    {
        $gallery = $this->galleryRepository->getOneById($galleryId);
        $this->breadcrumb->append($gallery['title'], 'acp/gallery/pictures/index/id_' . $galleryId);
        $this->title->setPageTitlePrefix($this->translator->t('gallery', 'admin_pictures_index'));
    }
}
