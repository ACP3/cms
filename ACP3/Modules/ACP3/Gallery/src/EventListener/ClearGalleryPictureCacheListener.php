<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\EventListener;

use ACP3\Core\Cache;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ClearGalleryPictureCacheListener implements EventSubscriberInterface
{
    /**
     * @var Cache
     */
    private $galleryCache;

    public function __construct(Cache $galleryCache)
    {
        $this->galleryCache = $galleryCache;
    }

    public function __invoke(): void
    {
        $this->galleryCache->deleteAll();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'gallery.model.gallery.after_save' => '__invoke',
            'gallery.model.gallery.after_delete' => '__invoke',
            'gallery.model.gallery_pictures.after_save' => '__invoke',
            'gallery.model.gallery_pictures.after_delete' => '__invoke',
        ];
    }
}
