<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Admin\Pictures;

use ACP3\Core;
use ACP3\Core\Controller\AbstractFrontendAction;
use ACP3\Core\DataGrid\QueryOption;
use ACP3\Modules\ACP3\Gallery;
use ACP3\Modules\ACP3\Gallery\Helpers;
use ACP3\Modules\ACP3\System\Installer\Schema;
use Symfony\Component\HttpFoundation\JsonResponse;

class Index extends AbstractFrontendAction
{
    /**
     * @var Gallery\Model\GalleryModel
     */
    protected $galleryModel;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Helper\ThumbnailGenerator
     */
    private $thumbnailGenerator;
    /**
     * @var \ACP3\Core\DataGrid\DataGrid
     */
    private $dataGrid;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryPicturesDataGridRepository
     */
    private $picturesDataGridRepository;
    /**
     * @var \ACP3\Core\Helpers\ResultsPerPage
     */
    private $resultsPerPage;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Helpers\ResultsPerPage $resultsPerPage,
        Gallery\Model\Repository\GalleryPicturesDataGridRepository $picturesDataGridRepository,
        Gallery\Model\GalleryModel $galleryModel,
        Gallery\Helper\ThumbnailGenerator $thumbnailGenerator,
        Core\DataGrid\DataGrid $dataGrid
    ) {
        parent::__construct($context);

        $this->galleryModel = $galleryModel;
        $this->thumbnailGenerator = $thumbnailGenerator;
        $this->dataGrid = $dataGrid;
        $this->picturesDataGridRepository = $picturesDataGridRepository;
        $this->resultsPerPage = $resultsPerPage;
    }

    /**
     * @param int $id
     *
     * @return array|JsonResponse
     *
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(int $id)
    {
        $gallery = $this->galleryModel->getOneById($id);

        if (!empty($gallery)) {
            $this->breadcrumb->append($gallery['title'], 'acp/gallery/pictures/index/id_' . $id);
            $this->title->setPageTitlePrefix($this->translator->t('gallery', 'admin_pictures_index'));

            $input = (new Core\DataGrid\Input())
                ->setUseAjax(true)
                ->setRepository($this->picturesDataGridRepository)
                ->setRecordsPerPage($this->resultsPerPage->getResultsPerPage(Schema::MODULE_NAME))
                ->setIdentifier('#gallery-pictures-data-grid')
                ->setResourcePathDelete('admin/gallery/pictures/delete/id_' . $id)
                ->setResourcePathEdit('admin/gallery/pictures/edit')
                ->setQueryOptions(new QueryOption('gallery_id', $id));

            $this->addDataGridColumns($input);

            $dataGrid = $this->dataGrid->render($input);
            if ($dataGrid instanceof JsonResponse) {
                return $dataGrid;
            }

            return \array_merge($dataGrid, ['gallery_id' => $id]);
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    /**
     * @param \ACP3\Core\DataGrid\Input $input
     */
    protected function addDataGridColumns(Core\DataGrid\Input $input)
    {
        $input
            ->addColumn([
                'label' => $this->translator->t('gallery', 'picture'),
                'type' => Core\DataGrid\ColumnRenderer\PictureColumnRenderer::class,
                'fields' => ['file'],
                'custom' => [
                    'callback' => function (string $fileName) {
                        return $this->thumbnailGenerator->generateThumbnail($fileName, 'thumb')->getFileWeb();
                    },
                ],
            ], 40)
            ->addColumn([
                'label' => $this->translator->t('gallery', 'title'),
                'type' => Core\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['title'],
            ], 35)
            ->addColumn([
                'label' => $this->translator->t('system', 'description'),
                'type' => Core\DataGrid\ColumnRenderer\TextColumnRenderer::class,
                'fields' => ['description'],
                'class' => 'datagrid-column__max-width',
            ], 30)
            ->addColumn([
                'label' => $this->translator->t('system', 'id'),
                'type' => Core\DataGrid\ColumnRenderer\RouteColumnRenderer::class,
                'fields' => ['id'],
                'primary' => true,
                'custom' => [
                    'path' => Helpers::URL_KEY_PATTERN_PICTURE,
                ],
                'class' => 'text-right',
            ], 10);

        if ($this->acl->hasPermission('admin/gallery/pictures/order')) {
            $input
                ->addColumn([
                    'label' => $this->translator->t('system', 'order'),
                    'type' => Core\DataGrid\ColumnRenderer\SortColumnRenderer::class,
                    'fields' => ['pic'],
                    'default_sort' => true,
                    'custom' => [
                        'route_sort_down' => 'acp/gallery/pictures/order/id_%d/action_down',
                        'route_sort_up' => 'acp/gallery/pictures/order/id_%d/action_up',
                    ],
                    'class' => 'text-center',
                ], 20);
        }
    }
}
