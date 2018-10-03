<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Frontend\Index;

use ACP3\Core;
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
     * Index constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext                 $context
     * @param \ACP3\Core\Date                                               $date
     * @param \ACP3\Core\Pagination                                         $pagination
     * @param \ACP3\Modules\ACP3\Gallery\Model\Repository\GalleryRepository $galleryRepository
     * @param \ACP3\Modules\ACP3\Gallery\Helper\ThumbnailGenerator          $thumbnailGenerator
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Date $date,
        Core\Pagination $pagination,
        Gallery\Model\Repository\GalleryRepository $galleryRepository,
        Gallery\Helper\ThumbnailGenerator $thumbnailGenerator
    ) {
        parent::__construct($context);

        $this->date = $date;
        $this->pagination = $pagination;
        $this->galleryRepository = $galleryRepository;
        $this->thumbnailGenerator = $thumbnailGenerator;
    }

    /**
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \ACP3\Core\Picture\Exception\PictureGenerateException
     */
    public function execute()
    {
        $this->setCacheResponseCacheable($this->config->getSettings(Schema::MODULE_NAME)['cache_lifetime']);

        $resultsPerPage = $this->resultsPerPage->getResultsPerPage(Gallery\Installer\Schema::MODULE_NAME);
        $time = $this->date->getCurrentDateTime();
        $this->pagination
            ->setResultsPerPage($resultsPerPage)
            ->setTotalResults($this->galleryRepository->countAll($time));

        return [
            'galleries' => $this->getGalleries($time, $resultsPerPage),
            'dateformat' => $this->settings['dateformat'],
            'pagination' => $this->pagination->render(),
        ];
    }

    /**
     * @param string $time
     * @param int    $resultsPerPage
     *
     * @return array
     *
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
