<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Gallery;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Gallery\Controller\Frontend\Index
 */
class Index extends AbstractAction
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\Pagination
     */
    protected $pagination;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\GalleryRepository
     */
    protected $galleryRepository;

    /**
     * Index constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext      $context
     * @param \ACP3\Core\Date                                    $date
     * @param \ACP3\Core\Pagination                              $pagination
     * @param \ACP3\Modules\ACP3\Gallery\Model\GalleryRepository $galleryRepository
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Date $date,
        Core\Pagination $pagination,
        Gallery\Model\GalleryRepository $galleryRepository)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->pagination = $pagination;
        $this->galleryRepository = $galleryRepository;
    }

    /**
     * @return array
     */
    public function execute()
    {
        $time = $this->date->getCurrentDateTime();

        $this->pagination->setTotalResults($this->galleryRepository->countAll($time));

        return [
            'galleries' => $this->galleryRepository->getAll($time, POS, $this->user->getEntriesPerPage()),
            'dateformat' => $this->settings['dateformat'],
            'pagination' => $this->pagination->render()
        ];
    }
}
