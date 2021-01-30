<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Gallery;

class Edit extends Core\Controller\AbstractWidgetAction implements Core\Controller\InvokableActionInterface
{
    /**
     * @var Gallery\Model\GalleryModel
     */
    private $galleryModel;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\ViewProviders\AdminGalleryEditViewProvider
     */
    private $adminGalleryEditViewProvider;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Gallery\Model\GalleryModel $galleryModel,
        Gallery\ViewProviders\AdminGalleryEditViewProvider $adminGalleryEditViewProvider
    ) {
        parent::__construct($context);

        $this->galleryModel = $galleryModel;
        $this->adminGalleryEditViewProvider = $adminGalleryEditViewProvider;
    }

    /**
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke(int $id): array
    {
        $gallery = $this->galleryModel->getOneById($id);

        if (!empty($gallery)) {
            return ($this->adminGalleryEditViewProvider)($gallery);
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
