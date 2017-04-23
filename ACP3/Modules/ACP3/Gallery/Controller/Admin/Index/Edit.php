<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Gallery;
use ACP3\Modules\ACP3\System\Installer\Schema;

class Edit extends Core\Controller\AbstractAdminAction
{
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Validation\GalleryFormValidation
     */
    protected $galleryFormValidation;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository
     */
    protected $pictureRepository;
    /**
     * @var Gallery\Model\GalleryModel
     */
    protected $galleryModel;
    /**
     * @var Core\View\Block\FormBlockInterface
     */
    private $block;

    /**
     * Edit constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param Core\View\Block\FormBlockInterface $block
     * @param \ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository $pictureRepository
     * @param Gallery\Model\GalleryModel $galleryModel
     * @param \ACP3\Modules\ACP3\Gallery\Validation\GalleryFormValidation $galleryFormValidation
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\View\Block\FormBlockInterface $block,
        Gallery\Model\Repository\PictureRepository $pictureRepository,
        Gallery\Model\GalleryModel $galleryModel,
        Gallery\Validation\GalleryFormValidation $galleryFormValidation
    ) {
        parent::__construct($context);

        $this->pictureRepository = $pictureRepository;
        $this->galleryModel = $galleryModel;
        $this->galleryFormValidation = $galleryFormValidation;
        $this->block = $block;
    }

    /**
     * @param int $id
     *
     * @return array
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($id)
    {
        $gallery = $this->galleryModel->getOneById($id);

        if (!empty($gallery)) {
            $data = $this->block
                ->setRequestData($this->request->getPost()->all())
                ->setData($gallery)
                ->render();

            return array_merge($data, $this->executeListPictures($id));
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    /**
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function executePost($id)
    {
        return $this->actionHelper->handleSaveAction(function () use ($id) {
            $formData = $this->request->getPost()->all();

            $this->galleryFormValidation
                ->setUriAlias(sprintf(Gallery\Helpers::URL_KEY_PATTERN_GALLERY, $id))
                ->validate($formData);

            $formData['user_id'] = $this->user->getUserId();

            return $this->galleryModel->save($formData, $id);
        });
    }

    /**
     * @param int $id
     *
     * @return array
     */
    protected function executeListPictures($id)
    {
        $pictures = $this->pictureRepository->getPicturesByGalleryId($id);

        /** @var Core\Helpers\DataGrid $dataGrid */
        $dataGrid = $this->get('core.helpers.data_grid');
        $dataGrid
            ->setResults($pictures)
            ->setRecordsPerPage($this->resultsPerPage->getResultsPerPage(Schema::MODULE_NAME))
            ->setIdentifier('#gallery-edit-data-grid')
            ->setResourcePathDelete('admin/gallery/pictures/delete/id_' . $id)
            ->setResourcePathEdit('admin/gallery/pictures/edit');

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
}
