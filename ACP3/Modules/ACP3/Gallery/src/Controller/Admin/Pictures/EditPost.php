<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Admin\Pictures;

use ACP3\Core;
use ACP3\Core\Helpers\FormAction;
use ACP3\Modules\ACP3\Gallery;
use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

class EditPost extends Core\Controller\AbstractWidgetAction
{
    public function __construct(
        Core\Controller\Context\Context $context,
        private readonly FormAction $actionHelper,
        private readonly Gallery\Helper\ThumbnailGenerator $thumbnailGenerator,
        private readonly Gallery\Model\PictureModel $pictureModel,
        private readonly Gallery\Validation\PictureFormValidation $pictureFormValidation,
        private readonly Core\Helpers\Upload $galleryUploadHelper
    ) {
        parent::__construct($context);
    }

    /**
     * @return array<string, mixed>|string|Response
     *
     * @throws ConnectionException
     * @throws Exception
     */
    public function __invoke(int $id): array|string|Response
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
