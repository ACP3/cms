<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Core\Controller\Exception\ResultNotExistsException;
use ACP3\Core\Helpers\ResultsPerPage;
use ACP3\Core\Pagination\Exception\InvalidPageException;
use ACP3\Modules\ACP3\Gallery;
use ACP3\Modules\ACP3\System\Installer\Schema;

class Index extends AbstractAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\Pagination
     */
    protected $pagination;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryRepository
     */
    protected $galleryRepository;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Helper\ThumbnailGenerator
     */
    private $thumbnailGenerator;
    /**
     * @var \ACP3\Core\Helpers\ResultsPerPage
     */
    private $resultsPerPage;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Date $date,
        ResultsPerPage $resultsPerPage,
        Core\Pagination $pagination,
        Gallery\Model\Repository\GalleryRepository $galleryRepository,
        Gallery\Helper\ThumbnailGenerator $thumbnailGenerator
    ) {
        parent::__construct($context);

        $this->date = $date;
        $this->pagination = $pagination;
        $this->galleryRepository = $galleryRepository;
        $this->thumbnailGenerator = $thumbnailGenerator;
        $this->resultsPerPage = $resultsPerPage;
    }

    /**
     * @return array
     *
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     * @throws \ACP3\Core\Picture\Exception\PictureGenerateException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute()
    {
        $this->setCacheResponseCacheable($this->config->getSettings(Schema::MODULE_NAME)['cache_lifetime']);

        $resultsPerPage = $this->resultsPerPage->getResultsPerPage(Gallery\Installer\Schema::MODULE_NAME);
        $time = $this->date->getCurrentDateTime();
        $this->pagination
            ->setResultsPerPage($resultsPerPage)
            ->setTotalResults($this->galleryRepository->countAll($time));

        try {
            return [
                'galleries' => $this->getGalleries($time, $resultsPerPage),
                'dateformat' => $this->settings['dateformat'],
                'pagination' => $this->pagination->render(),
            ];
        } catch (InvalidPageException $e) {
            throw new ResultNotExistsException();
        }
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
