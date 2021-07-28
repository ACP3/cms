<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Modules\Helper\Action;
use ACP3\Modules\ACP3\Gallery;

class Delete extends Core\Controller\AbstractWidgetAction
{
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository
     */
    private $pictureRepository;
    /**
     * @var Gallery\Model\GalleryModel
     */
    private $galleryModel;
    /**
     * @var \ACP3\Core\Modules\Helper\Action
     */
    private $actionHelper;
    /**
     * @var Gallery\Helper\ThumbnailGenerator
     */
    private $thumbnailGenerator;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Action $actionHelper,
        Gallery\Helper\ThumbnailGenerator $thumbnailGenerator,
        Gallery\Model\GalleryModel $galleryModel,
        Gallery\Model\Repository\PictureRepository $pictureRepository
    ) {
        parent::__construct($context);

        $this->pictureRepository = $pictureRepository;
        $this->galleryModel = $galleryModel;
        $this->actionHelper = $actionHelper;
        $this->thumbnailGenerator = $thumbnailGenerator;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute(?string $action = null)
    {
        return $this->actionHelper->handleDeleteAction(
            $action,
            function (array $items) {
                foreach ($items as $item) {
                    $pictures = $this->pictureRepository->getPicturesByGalleryId($item);
                    foreach ($pictures as $row) {
                        $this->thumbnailGenerator->removePictureFromFilesystem($row['file']);
                    }
                }

                return $this->galleryModel->delete($items);
            }
        );
    }
}
