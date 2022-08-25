<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Admin\Pictures;

use ACP3\Core\Controller\AbstractWidgetAction;
use ACP3\Core\Controller\Context\Context;
use ACP3\Core\Helpers\FormAction;
use ACP3\Modules\ACP3\Gallery\Model\PictureModel;
use ACP3\Modules\ACP3\Gallery\Services\GalleryPictureUpsertService;
use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\Response;

class EditPost extends AbstractWidgetAction
{
    public function __construct(
        Context $context,
        private readonly FormAction $actionHelper,
        private readonly GalleryPictureUpsertService $galleryPictureUpsertService,
        private readonly PictureModel $pictureModel,
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
            fn () => $this->galleryPictureUpsertService->upsert($this->request->getPost()->all(), $this->request->getFiles()->get('file'), $id),
            'acp/gallery/pictures/index/id_' . $picture['gallery_id']
        );
    }
}
