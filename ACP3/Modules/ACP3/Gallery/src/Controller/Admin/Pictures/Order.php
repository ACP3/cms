<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Admin\Pictures;

use ACP3\Core;
use ACP3\Modules\ACP3\Gallery;

class Order extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository
     */
    private $pictureRepository;
    /**
     * @var \ACP3\Core\Http\RedirectResponse
     */
    private $redirectResponse;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\PictureModel
     */
    private $pictureModel;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Http\RedirectResponse $redirectResponse,
        Gallery\Model\Repository\PictureRepository $pictureRepository,
        Gallery\Model\PictureModel $pictureModel
    ) {
        parent::__construct($context);

        $this->pictureRepository = $pictureRepository;
        $this->redirectResponse = $redirectResponse;
        $this->pictureModel = $pictureModel;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(int $id, string $action)
    {
        if (($action === 'up' || $action === 'down') && $this->pictureRepository->pictureExists($id) === true) {
            if ($action === 'up') {
                $this->pictureModel->moveUp($id);
            } else {
                $this->pictureModel->moveDown($id);
            }

            $galleryId = $this->pictureRepository->getGalleryIdFromPictureId($id);

            return $this->redirectResponse->temporary('acp/gallery/pictures/index/id_' . $galleryId);
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
