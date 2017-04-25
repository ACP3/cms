<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Gallery\View\Block\Frontend;


use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View\Block\AbstractBlock;
use ACP3\Core\View\Block\Context\BlockContext;
use ACP3\Modules\ACP3\Gallery\Cache;
use ACP3\Modules\ACP3\Gallery\Installer\Schema;
use ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryRepository;

class GalleryPicturesListingBlock extends AbstractBlock
{
    /**
     * @var SettingsInterface
     */
    private $settings;
    /**
     * @var GalleryRepository
     */
    private $galleryRepository;
    /**
     * @var Cache
     */
    private $galleryCache;

    /**
     * GalleryPicturesListingBlock constructor.
     * @param BlockContext $context
     * @param SettingsInterface $settings
     * @param GalleryRepository $galleryRepository
     * @param Cache $galleryCache
     */
    public function __construct(
        BlockContext $context,
        SettingsInterface $settings,
        GalleryRepository $galleryRepository,
        Cache $galleryCache
    ) {
        parent::__construct($context);

        $this->settings = $settings;
        $this->galleryRepository = $galleryRepository;
        $this->galleryCache = $galleryCache;
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $data = $this->getData();

        $this->breadcrumb
            ->append($this->translator->t('gallery', 'gallery'), 'gallery')
            ->append($this->galleryRepository->getGalleryTitle($data['gallery_id']));

        return [
            'pictures' => $this->galleryCache->getCache($data['gallery_id']),
            'overlay' => (int)$this->settings->getSettings(Schema::MODULE_NAME)['overlay']
        ];
    }
}
