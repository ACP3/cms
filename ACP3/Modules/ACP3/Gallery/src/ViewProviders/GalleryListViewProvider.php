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
use ACP3\Modules\ACP3\Gallery\Repository\GalleryRepository;

class GalleryListViewProvider
{
    public function __construct(private readonly Date $date, private readonly GalleryRepository $galleryRepository, private readonly Pagination $pagination, private readonly ResultsPerPage $resultsPerPage, private readonly SettingsInterface $settings, private readonly ThumbnailGenerator $thumbnailGenerator)
    {
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \ACP3\Core\Picture\Exception\PictureGenerateException
     * @throws \Doctrine\DBAL\Exception
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
     * @return array<string, mixed>
     *
     * @throws \ACP3\Core\Picture\Exception\PictureGenerateException
     * @throws \Doctrine\DBAL\Exception
     */
    private function getGalleries(string $time, int $resultsPerPage): array
    {
        $galleries = $this->galleryRepository->getAll(
            $time,
            $this->pagination->getResultsStartOffset(),
            $resultsPerPage
        );

        foreach ($galleries as &$gallery) {
            if ($gallery['file'] === null) {
                continue;
            }

            $gallery['file'] = $this->thumbnailGenerator->generateThumbnail($gallery['file'], 'thumb')->getFileWeb();
        }

        return $galleries;
    }
}
