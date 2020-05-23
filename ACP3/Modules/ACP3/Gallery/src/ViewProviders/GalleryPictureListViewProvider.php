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
use ACP3\Modules\ACP3\Gallery\Cache;
use ACP3\Modules\ACP3\Gallery\Installer\Schema as GallerySchema;
use ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryRepository;

class GalleryPictureListViewProvider
{
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Cache
     */
    private $galleryCache;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryRepository
     */
    private $galleryRepository;
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

    public function __construct(
        Cache $galleryCache,
        GalleryRepository $galleryRepository,
        RequestInterface $request,
        SettingsInterface $settings,
        Steps $breadcrumb,
        Title $title,
        Translator $translator
    ) {
        $this->galleryCache = $galleryCache;
        $this->galleryRepository = $galleryRepository;
        $this->request = $request;
        $this->settings = $settings;
        $this->breadcrumb = $breadcrumb;
        $this->title = $title;
        $this->translator = $translator;
    }

    /**
     * @throws \ACP3\Core\Picture\Exception\PictureGenerateException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke(int $galleryId): array
    {
        $settings = $this->settings->getSettings(GallerySchema::MODULE_NAME);
        $gallery = $this->galleryRepository->getOneById($galleryId);

        $this->breadcrumb
            ->append($this->translator->t('gallery', 'gallery'), 'gallery')
            ->append(
                $gallery['title'],
                $this->request->getQuery()
            );
        $this->title->setPageTitle($gallery['title']);

        return [
            'pictures' => $this->galleryCache->getCache($galleryId),
            'gallery' => $gallery,
            'overlay' => (int) $settings['overlay'],
        ];
    }
}
