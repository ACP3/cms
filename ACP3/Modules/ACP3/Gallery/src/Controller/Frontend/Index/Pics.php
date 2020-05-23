<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Gallery;
use ACP3\Modules\ACP3\System\Installer\Schema;

class Pics extends Core\Controller\AbstractFrontendAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var \ACP3\Core\Date
     */
    private $date;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryRepository
     */
    private $galleryRepository;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\ViewProviders\GalleryPictureListViewProvider
     */
    private $galleryPictureListViewProvider;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Date $date,
        Gallery\Model\Repository\GalleryRepository $galleryRepository,
        Gallery\ViewProviders\GalleryPictureListViewProvider $galleryPictureListViewProvider
    ) {
        parent::__construct($context);

        $this->date = $date;
        $this->galleryRepository = $galleryRepository;
        $this->galleryPictureListViewProvider = $galleryPictureListViewProvider;
    }

    /**
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     * @throws \ACP3\Core\Picture\Exception\PictureGenerateException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(int $id): array
    {
        if ($this->galleryRepository->galleryExists($id, $this->date->getCurrentDateTime()) === true) {
            $this->setCacheResponseCacheable($this->config->getSettings(Schema::MODULE_NAME)['cache_lifetime']);

            return ($this->galleryPictureListViewProvider)($id);
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
