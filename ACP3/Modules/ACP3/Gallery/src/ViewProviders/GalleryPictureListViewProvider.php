<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\ViewProviders;

use ACP3\Core\Breadcrumb\Steps;
use ACP3\Core\Breadcrumb\Title;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Gallery\Installer\Schema as GallerySchema;
use ACP3\Modules\ACP3\Gallery\Services\GalleryServiceInterface;

class GalleryPictureListViewProvider
{
    public function __construct(private GalleryServiceInterface $galleryService, private RequestInterface $request, private SettingsInterface $settings, private Steps $breadcrumb, private Title $title, private Translator $translator)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function __invoke(int $galleryId): array
    {
        $settings = $this->settings->getSettings(GallerySchema::MODULE_NAME);
        $galleryWithPictures = $this->galleryService->getGalleryWithPictures($galleryId);

        $this->breadcrumb
            ->append($this->translator->t('gallery', 'gallery'), 'gallery')
            ->append(
                $galleryWithPictures['title'],
                $this->request->getQuery()
            );
        $this->title->setPageTitle($galleryWithPictures['title']);

        return [
            'gallery' => $galleryWithPictures,
            'overlay' => (int) $settings['overlay'],
        ];
    }
}
