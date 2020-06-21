<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Admin\Pictures;

use ACP3\Core;
use ACP3\Modules\ACP3\Gallery;

class Edit extends Core\Controller\AbstractFrontendAction implements Core\Controller\InvokableActionInterface
{
    /**
     * @var Gallery\Model\PictureModel
     */
    private $pictureModel;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\ViewProviders\AdminGalleryPictureEditViewProvider
     */
    private $adminGalleryPictureEditViewProvider;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Gallery\Model\PictureModel $pictureModel,
        Gallery\ViewProviders\AdminGalleryPictureEditViewProvider $adminGalleryPictureEditViewProvider
    ) {
        parent::__construct($context);

        $this->pictureModel = $pictureModel;
        $this->adminGalleryPictureEditViewProvider = $adminGalleryPictureEditViewProvider;
    }

    /**
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke(int $id): array
    {
        $picture = $this->pictureModel->getOneById($id);

        if (!empty($picture)) {
            return ($this->adminGalleryPictureEditViewProvider)($picture);
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
