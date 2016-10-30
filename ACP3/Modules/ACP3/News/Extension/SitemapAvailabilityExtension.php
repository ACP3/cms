<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\News\Extension;


use ACP3\Core\Date;
use ACP3\Core\Router\Router;
use ACP3\Modules\ACP3\News\Installer\Schema;
use ACP3\Modules\ACP3\News\Model\Repository\NewsRepository;
use ACP3\Modules\ACP3\Seo\Extension\AbstractSitemapAvailabilityExtension;
use ACP3\Modules\ACP3\Seo\Helper\MetaStatements;

class SitemapAvailabilityExtension extends AbstractSitemapAvailabilityExtension
{
    /**
     * @var Date
     */
    protected $date;
    /**
     * @var NewsRepository
     */
    protected $newsRepository;

    /**
     * SitemapAvailabilityExtension constructor.
     * @param Date $date
     * @param Router $router
     * @param NewsRepository $newsRepository
     * @param MetaStatements $metaStatements
     */
    public function __construct(
        Date $date,
        Router $router,
        NewsRepository $newsRepository,
        MetaStatements $metaStatements
    ) {
        parent::__construct($router, $metaStatements);

        $this->date = $date;
        $this->newsRepository = $newsRepository;
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        return Schema::MODULE_NAME;
    }

    public function fetchSitemapUrls()
    {
        $this->addUrl('news/index/index');

        foreach ($this->newsRepository->getAll($this->date->getCurrentDateTime()) as $result) {
            $this->addUrl('news/index/details/id_' . $result['id'], $result['start']);
        }
    }
}
