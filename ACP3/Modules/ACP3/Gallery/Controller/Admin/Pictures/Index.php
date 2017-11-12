<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Admin\Pictures;

use ACP3\Core;
use ACP3\Modules\ACP3\Gallery;
use ACP3\Modules\ACP3\Gallery\Helpers;
use ACP3\Modules\ACP3\System\Installer\Schema;
use ACP3\Core\Controller\AbstractFrontendAction;

class Index extends AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository
     */
    protected $pictureRepository;
    /**
     * @var Gallery\Model\GalleryModel
     */
    protected $galleryModel;

    /**
     * Edit constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param \ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository $pictureRepository
     * @param Gallery\Model\GalleryModel $galleryModel
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Gallery\Model\Repository\PictureRepository $pictureRepository,
        Gallery\Model\GalleryModel $galleryModel
    ) {
        parent::__construct($context);

        $this->pictureRepository = $pictureRepository;
        $this->galleryModel = $galleryModel;
    }

    /**
     * @param int $id
     * @return array
     * @throws Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($id)
    {
        $gallery = $this->galleryModel->getOneById($id);

        if (!empty($gallery)) {
            $this->breadcrumb->append($gallery['title'], 'acp/gallery/pictures/index/id_' . $id);
            $this->title->setPageTitlePrefix($this->translator->t('gallery', 'admin_pictures_index'));

            $pictures = $this->pictureRepository->getPicturesByGalleryId($id);

            /** @var Core\Helpers\DataGrid $dataGrid */
            $dataGrid = $this->get('core.helpers.data_grid');
            $dataGrid
                ->setResults($pictures)
                ->setRecordsPerPage($this->resultsPerPage->getResultsPerPage(Schema::MODULE_NAME))
                ->setIdentifier('#gallery-pictures-data-grid')
                ->setResourcePathDelete('admin/gallery/pictures/delete/id_' . $id)
                ->setResourcePathEdit('admin/gallery/pictures/edit');

            $this->addDataGridColumns($dataGrid);

            return [
                'gallery_id' => $id,
                'grid' => $dataGrid->render(),
                'show_mass_delete_button' => $dataGrid->countDbResults() > 0
            ];
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    /**
     * @param Core\Helpers\DataGrid $dataGrid
     */
    protected function addDataGridColumns(Core\Helpers\DataGrid $dataGrid)
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
                'type' => Core\Helpers\DataGrid\ColumnRenderer\RouteColumnRenderer::class,
                'fields' => ['id'],
                'primary' => true,
                'custom' => [
                    'path' => Helpers::URL_KEY_PATTERN_PICTURE
                ]
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
}
