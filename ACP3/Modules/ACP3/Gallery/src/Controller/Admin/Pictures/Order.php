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
     * @var \ACP3\Core\Helpers\Sort
     */
    protected $sortHelper;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Cache
     */
    protected $galleryCache;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository
     */
    protected $pictureRepository;
    /**
     * @var \ACP3\Core\Http\RedirectResponse
     */
    private $redirectResponse;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Http\RedirectResponse $redirectResponse,
        Core\Helpers\Sort $sortHelper,
        Gallery\Model\Repository\PictureRepository $pictureRepository,
        Gallery\Cache $galleryCache
    ) {
        parent::__construct($context);

        $this->sortHelper = $sortHelper;
        $this->pictureRepository = $pictureRepository;
        $this->galleryCache = $galleryCache;
        $this->redirectResponse = $redirectResponse;
    }

    /**
     * @param int    $id
     * @param string $action
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($id, $action)
    {
        if (($action === 'up' || $action === 'down') && $this->pictureRepository->pictureExists($id) === true) {
            if ($action === 'up') {
                $this->sortHelper->up(Gallery\Model\Repository\PictureRepository::TABLE_NAME, 'id', 'pic', $id, 'gallery_id');
            } else {
                $this->sortHelper->down(Gallery\Model\Repository\PictureRepository::TABLE_NAME, 'id', 'pic', $id, 'gallery_id');
            }

            $galleryId = $this->pictureRepository->getGalleryIdFromPictureId($id);

            $this->galleryCache->saveCache($galleryId);

            Core\Cache\Purge::doPurge($this->appPath->getCacheDir() . 'http');

            return $this->redirectResponse->temporary('acp/gallery/pictures/index/id_' . $galleryId);
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
