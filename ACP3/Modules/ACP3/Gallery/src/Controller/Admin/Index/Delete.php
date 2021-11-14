<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Helpers\FormAction;
use ACP3\Modules\ACP3\Gallery;

class Delete extends Core\Controller\AbstractWidgetAction
{
    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        private FormAction $actionHelper,
        private Gallery\Helper\ThumbnailGenerator $thumbnailGenerator,
        private Gallery\Model\GalleryModel $galleryModel,
        private Gallery\Repository\PictureRepository $pictureRepository
    ) {
        parent::__construct($context);
    }

    public function __invoke(?string $action = null): array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
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
