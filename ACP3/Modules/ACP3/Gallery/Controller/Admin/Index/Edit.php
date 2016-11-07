<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Gallery;
use ACP3\Modules\ACP3\System\Installer\Schema;

/**
 * Class Edit
 * @package ACP3\Modules\ACP3\Gallery\Controller\Admin\Index
 */
class Edit extends Core\Controller\AbstractAdminAction
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
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
     * Edit constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext $context
     * @param \ACP3\Core\Helpers\FormToken $formTokenHelper
     * @param \ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository $pictureRepository
     * @param Gallery\Model\GalleryModel $galleryModel
     * @param \ACP3\Modules\ACP3\Gallery\Validation\GalleryFormValidation $galleryFormValidation
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Core\Helpers\FormToken $formTokenHelper,
        Gallery\Model\Repository\PictureRepository $pictureRepository,
        Gallery\Model\GalleryModel $galleryModel,
        Gallery\Validation\GalleryFormValidation $galleryFormValidation
    ) {
        parent::__construct($context);

        $this->formTokenHelper = $formTokenHelper;
        $this->pictureRepository = $pictureRepository;
        $this->galleryModel = $galleryModel;
        $this->galleryFormValidation = $galleryFormValidation;
    }

    /**
     * @param int $id
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($id)
    {
        $gallery = $this->galleryModel->getOneById($id);

        if (!empty($gallery)) {
            $this->title->setPageTitlePostfix($gallery['title']);

            if ($this->request->getPost()->count() !== 0) {
                return $this->executePost($this->request->getPost()->all(), $id);
            }

            return array_merge(
                [
                    'gallery_id' => $id,
                    'form' => array_merge($gallery, $this->request->getPost()->all()),
                    'form_token' => $this->formTokenHelper->renderFormToken(),
                    'SEO_URI_PATTERN' => Gallery\Helpers::URL_KEY_PATTERN_GALLERY,
                    'SEO_ROUTE_NAME' => sprintf(Gallery\Helpers::URL_KEY_PATTERN_GALLERY, $id)

                ],
                $this->executeListPictures($id)
            );
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
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
            ->setIdentifier('#acp-table')
            ->setResourcePathDelete('admin/gallery/pictures/delete/id_' . $id)
            ->setResourcePathEdit('admin/gallery/pictures/edit');

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

        return [
            'grid' => $dataGrid->render(),
            'show_mass_delete_button' => $dataGrid->countDbResults() > 0
        ];
    }

    /**
     * @param array $formData
     * @param int   $galleryId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData, $galleryId)
    {
        return $this->actionHelper->handleEditPostAction(function () use ($formData, $galleryId) {
            $this->galleryFormValidation
                ->setUriAlias(sprintf(Gallery\Helpers::URL_KEY_PATTERN_GALLERY, $galleryId))
                ->validate($formData);

            $formData['user_id'] = $this->user->getUserId();

            return $this->galleryModel->save($formData, $galleryId);
        });
    }
}
