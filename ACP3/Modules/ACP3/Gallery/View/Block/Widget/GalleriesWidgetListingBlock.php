<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Gallery\View\Block\Widget;


use ACP3\Core\Date;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View\Block\AbstractBlock;
use ACP3\Core\View\Block\Context\BlockContext;
use ACP3\Modules\ACP3\Gallery\Installer\Schema;
use ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryRepository;

class GalleriesWidgetListingBlock extends AbstractBlock
{
    /**
     * @var Date
     */
    private $date;
    /**
     * @var SettingsInterface
     */
    private $settings;
    /**
     * @var GalleryRepository
     */
    private $galleryRepository;

    /**
     * GalleriesWidgetListingBlock constructor.
     * @param BlockContext $context
     * @param Date $date
     * @param SettingsInterface $settings
     * @param GalleryRepository $galleryRepository
     */
    public function __construct(
        BlockContext $context,
        Date $date,
        SettingsInterface $settings,
        GalleryRepository $galleryRepository
    ) {
        parent::__construct($context);

        $this->date = $date;
        $this->settings = $settings;
        $this->galleryRepository = $galleryRepository;
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

        return [
            'sidebar_galleries' => $this->galleryRepository->getAll(
                $this->date->getCurrentDateTime(),
                $settings['sidebar']
            )
        ];
    }
}
