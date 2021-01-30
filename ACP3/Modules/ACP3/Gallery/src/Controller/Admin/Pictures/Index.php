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
    /**
     * @var Gallery\Model\GalleryModel
     */
    private $galleryModel;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\ViewProviders\PictureDataGridViewProvider
     */
    private $pictureDataGridViewProvider;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Gallery\Model\GalleryModel $galleryModel,
        Gallery\ViewProviders\PictureDataGridViewProvider $pictureDataGridViewProvider
    ) {
        parent::__construct($context);

        $this->galleryModel = $galleryModel;
        $this->pictureDataGridViewProvider = $pictureDataGridViewProvider;
    }

    /**
     * @return array|array[]|\Symfony\Component\HttpFoundation\JsonResponse
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(int $id)
    {
        $gallery = $this->galleryModel->getOneById($id);

        if (!empty($gallery)) {
            return ($this->pictureDataGridViewProvider)($id, $gallery);
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
