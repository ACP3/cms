<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Admin\Pictures;

use ACP3\Core;
use ACP3\Modules\ACP3\Gallery;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Create extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    private $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryRepository
     */
    private $galleryRepository;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Validation\PictureFormValidation
     */
    private $pictureFormValidation;
    /**
     * @var Gallery\Model\PictureModel
     */
    private $pictureModel;
    /**
     * @var \ACP3\Core\Helpers\Upload
     */
    private $galleryUploadHelper;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Helpers\FormToken $formTokenHelper,
        Gallery\Model\Repository\GalleryRepository $galleryRepository,
        Gallery\Model\PictureModel $pictureModel,
        Gallery\Validation\PictureFormValidation $pictureFormValidation,
        Core\Helpers\Upload $galleryUploadHelper
    ) {
        parent::__construct($context);

        $this->formTokenHelper = $formTokenHelper;
        $this->galleryRepository = $galleryRepository;
        $this->pictureFormValidation = $pictureFormValidation;
        $this->pictureModel = $pictureModel;
        $this->galleryUploadHelper = $galleryUploadHelper;
    }

    /**
     * @return array
     *
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(int $id)
    {
        if ($this->galleryRepository->galleryExists($id) === true) {
            $gallery = $this->galleryRepository->getGalleryTitle($id);

            $this->breadcrumb
                ->append($gallery, 'acp/gallery/pictures/index/id_' . $id)
                ->append(
                    $this->translator->t('gallery', 'admin_pictures_create'),
                    $this->request->getQuery()
                );

            return [
                'form' => \array_merge(['title' => '', 'description' => ''], $this->request->getPost()->all()),
                'gallery_id' => $id,
                'form_token' => $this->formTokenHelper->renderFormToken(),
            ];
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function executePost(int $id)
    {
        return $this->actionHelper->handleSaveAction(
            function () use ($id) {
                $formData = $this->request->getPost()->all();

                /** @var UploadedFile $file */
                $file = $this->request->getFiles()->get('file');

                $this->pictureFormValidation
                    ->setFileRequired(true)
                    ->setFile($file)
                    ->validate($formData);

                $result = $this->galleryUploadHelper->moveFile($file->getPathname(), $file->getClientOriginalName());

                $formData['file'] = $result['name'];
                $formData['gallery_id'] = $id;

                return $this->pictureModel->save($formData);
            },
            'acp/gallery/pictures/index/id_' . $id
        );
    }
}
