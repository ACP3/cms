<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Gallery\View\Block\Frontend;

use ACP3\Core\Date;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View\Block\AbstractListingBlock;
use ACP3\Core\View\Block\Context\ListingBlockContext;
use ACP3\Modules\ACP3\Gallery\Installer\Schema;
use ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryRepository;

class GalleriesListingBlock extends AbstractListingBlock
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
     * GalleriesListingBlock constructor.
     * @param ListingBlockContext $context
     * @param Date $date
     * @param SettingsInterface $settings
     * @param GalleryRepository $galleryRepository
     */
    public function __construct(
        ListingBlockContext $context,
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
    protected function getModuleName(): string
    {
        return Schema::MODULE_NAME;
    }

    /**
     * @inheritdoc
     */
    protected function getTotalResults(): int
    {
        return $this->galleryRepository->countAll($this->date->getCurrentDateTime());
    }

    /**
     * @inheritdoc
     */
    protected function getResults(int $resultsPerPage): array
    {
        return $this->galleryRepository->getAll(
            $this->date->getCurrentDateTime(),
            $this->pagination->getResultsStartOffset(),
            $resultsPerPage
        );
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $resultsPerPage = $this->getResultsPerPage();
        return [
            'galleries' => $this->getResults($resultsPerPage),
            'dateformat' => $this->settings->getSettings($this->getModuleName())['dateformat'],
            'pagination' => $this->pagination->render()
        ];
    }
}
