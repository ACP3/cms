<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\ViewProviders;

use ACP3\Core\Date;
use ACP3\Core\Helpers\ResultsPerPage;
use ACP3\Core\Pagination;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Gallery\Helper\ThumbnailGenerator;
use ACP3\Modules\ACP3\Gallery\Installer\Schema as GallerySchema;
use ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryRepository;

class GalleryListViewProvider
{
    /**
     * @var \ACP3\Core\Date
     */
    private $date;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryRepository
     */
    private $galleryRepository;
    /**
     * @var \ACP3\Core\Pagination
     */
    private $pagination;
    /**
     * @var \ACP3\Core\Helpers\ResultsPerPage
     */
    private $resultsPerPage;
    /**
     * @var \ACP3\Core\Settings\SettingsInterface
     */
    private $settings;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Helper\ThumbnailGenerator
     */
    private $thumbnailGenerator;

    public function __construct(
        Date $date,
        GalleryRepository $galleryRepository,
        Pagination $pagination,
        ResultsPerPage $resultsPerPage,
        SettingsInterface $settings,
        ThumbnailGenerator $thumbnailGenerator
    ) {
        $this->date = $date;
        $this->galleryRepository = $galleryRepository;
        $this->pagination = $pagination;
        $this->resultsPerPage = $resultsPerPage;
        $this->settings = $settings;
        $this->thumbnailGenerator = $thumbnailGenerator;
    }

    /**
     * @throws \ACP3\Core\Picture\Exception\PictureGenerateException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke(): array
    {
        $settings = $this->settings->getSettings(GallerySchema::MODULE_NAME);
        $resultsPerPage = $this->resultsPerPage->getResultsPerPage(GallerySchema::MODULE_NAME);
        $time = $this->date->getCurrentDateTime();
        $this->pagination
            ->setResultsPerPage($resultsPerPage)
            ->setTotalResults($this->galleryRepository->countAll($time));

        return [
            'galleries' => $this->getGalleries($time, $resultsPerPage),
            'dateformat' => $settings['dateformat'],
            'pagination' => $this->pagination->render(),
        ];
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     * @throws \ACP3\Core\Picture\Exception\PictureGenerateException
     */
    private function getGalleries(string $time, int $resultsPerPage): array
    {
        $galleries = $this->galleryRepository->getAll(
            $time,
            $this->pagination->getResultsStartOffset(),
            $resultsPerPage
        );

        foreach ($galleries as &$gallery) {
            $gallery['file'] = $this->thumbnailGenerator->generateThumbnail($gallery['file'], 'thumb')->getFileWeb();
        }

        return $galleries;
    }
}
