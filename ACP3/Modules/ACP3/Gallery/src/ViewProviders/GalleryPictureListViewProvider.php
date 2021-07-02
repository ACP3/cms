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
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\Settings\SettingsInterface
     */
    private $settings;
    /**
     * @var \ACP3\Core\Breadcrumb\Steps
     */
    private $breadcrumb;
    /**
     * @var \ACP3\Core\Breadcrumb\Title
     */
    private $title;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;
    /**
     * @var GalleryServiceInterface
     */
    private $galleryService;

    public function __construct(
        GalleryServiceInterface $galleryService,
        RequestInterface $request,
        SettingsInterface $settings,
        Steps $breadcrumb,
        Title $title,
        Translator $translator
    ) {
        $this->request = $request;
        $this->settings = $settings;
        $this->breadcrumb = $breadcrumb;
        $this->title = $title;
        $this->translator = $translator;
        $this->galleryService = $galleryService;
    }

    /**
     * @throws \ACP3\Core\Picture\Exception\PictureGenerateException
     * @throws \Doctrine\DBAL\Exception
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
            // @deprecated since version 5.19.0, to be removed with version 6.0.0. Use $gallery['pictures'] instead
            'pictures' => $galleryWithPictures['pictures'],
        ];
    }
}
