<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Admin\Pictures;

use ACP3\Core;
use ACP3\Core\Controller\AbstractWidgetAction;
use ACP3\Modules\ACP3\Gallery;

class Index extends AbstractWidgetAction
{
    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        private Gallery\Model\GalleryModel $galleryModel,
        private Gallery\ViewProviders\PictureDataGridViewProvider $pictureDataGridViewProvider
    ) {
        parent::__construct($context);
    }

    /**
     * @return array|array[]|\Symfony\Component\HttpFoundation\JsonResponse
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(int $id): array|\Symfony\Component\HttpFoundation\JsonResponse
    {
        $gallery = $this->galleryModel->getOneById($id);

        if (!empty($gallery)) {
            return ($this->pictureDataGridViewProvider)($id, $gallery);
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
