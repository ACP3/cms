<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Admin\Pictures;

use ACP3\Core;
use ACP3\Core\Modules\Helper\Action;
use ACP3\Modules\ACP3\Gallery;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class EditPost extends Core\Controller\AbstractWidgetAction implements Core\Controller\InvokableActionInterface
{
    /**
     * @var Gallery\Helper\ThumbnailGenerator
     */
    private $thumbnailGenerator;
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
    /**
     * @var \ACP3\Core\Modules\Helper\Action
     */
    private $actionHelper;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Action $actionHelper,
        Gallery\Helper\ThumbnailGenerator $thumbnailGenerator,
        Gallery\Model\PictureModel $pictureModel,
        Gallery\Validation\PictureFormValidation $pictureFormValidation,
        Core\Helpers\Upload $galleryUploadHelper
    ) {
        parent::__construct($context);

        $this->thumbnailGenerator = $thumbnailGenerator;
        $this->pictureFormValidation = $pictureFormValidation;
        $this->pictureModel = $pictureModel;
        $this->galleryUploadHelper = $galleryUploadHelper;
        $this->actionHelper = $actionHelper;
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(int $id)
    {
        $picture = $this->pictureModel->getOneById($id);

        return $this->actionHelper->handleSaveAction(
            function () use ($picture, $id) {
                $formData = $this->request->getPost()->all();
                /** @var UploadedFile $file */
                $file = $this->request->getFiles()->get('file');

                $this->pictureFormValidation
                    ->setFileRequired(false)
                    ->setFile($file)
                    ->validate($formData);

                if ($file !== null) {
                    $result = $this->galleryUploadHelper->moveFile($file->getPathname(), $file->getClientOriginalName());

                    $this->thumbnailGenerator->removePictureFromFilesystem($picture['file']);

                    $formData['file'] = $result['name'];
                }

                return $this->pictureModel->save($formData, $id);
            },
            'acp/gallery/pictures/index/id_' . $picture['gallery_id']
        );
    }
}
