<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Widget\Index;

use ACP3\Core\Cache\CacheResponseTrait;
use ACP3\Core\Controller\AbstractWidgetAction;
use ACP3\Core\Controller\Context\WidgetContext;
use ACP3\Modules\ACP3\Gallery\Cache;
use ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryRepository;
use ACP3\Modules\ACP3\System\Installer\Schema;

class Pictures extends AbstractWidgetAction
{
    use CacheResponseTrait;

    /**
     * @var GalleryRepository
     */
    private $galleryRepository;
    /**
     * @var Cache
     */
    private $galleryCache;

    /**
     * Pictures constructor.
     */
    public function __construct(
        WidgetContext $context,
        GalleryRepository $galleryRepository,
        Cache $galleryCache
    ) {
        parent::__construct($context);

        $this->galleryRepository = $galleryRepository;
        $this->galleryCache = $galleryCache;
    }

    /**
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \ACP3\Core\Picture\Exception\PictureGenerateException
     */
    public function execute(int $id, string $template = '')
    {
        $this->setCacheResponseCacheable($this->config->getSettings(Schema::MODULE_NAME)['cache_lifetime']);

        $this->setTemplate(\urldecode($template));

        return [
            'gallery' => $this->galleryRepository->getOneById($id),
            'pictures' => $this->galleryCache->getCache($id),
        ];
    }
}
