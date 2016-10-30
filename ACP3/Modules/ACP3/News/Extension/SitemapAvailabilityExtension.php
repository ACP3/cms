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
use Thepixeldeveloper\Sitemap\Url;

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
        $this->date = $date;
        $this->newsRepository = $newsRepository;

        parent::__construct($router, $metaStatements);
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        return Schema::MODULE_NAME;
    }

    /**
     * @return Url[]
     */
    public function fetchSitemapItems()
    {
        foreach ($this->newsRepository->getAll($this->date->getCurrentDateTime()) as $result) {
            $routeName = 'news/index/details/id_' . $result['id'];

            $this->addUrl($routeName, $result['start']);
        }

        return $this->getUrls();
    }
}
