<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Admin\Pictures;

use ACP3\Core;
use ACP3\Core\Helpers\FormAction;
use ACP3\Modules\ACP3\Gallery;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class EditPost extends Core\Controller\AbstractWidgetAction
{
    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        private FormAction $actionHelper,
        private Gallery\Helper\ThumbnailGenerator $thumbnailGenerator,
        private Gallery\Model\PictureModel $pictureModel,
        private Gallery\Validation\PictureFormValidation $pictureFormValidation,
        private Core\Helpers\Upload $galleryUploadHelper
    ) {
        parent::__construct($context);
    }

    /**
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(int $id): array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
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
