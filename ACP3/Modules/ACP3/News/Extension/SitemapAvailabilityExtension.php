<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\News\Extension;

use ACP3\Core\Date;
use ACP3\Core\Router\RouterInterface;
use ACP3\Modules\ACP3\News\Helpers;
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
     * @param RouterInterface $router
     * @param NewsRepository $newsRepository
     * @param MetaStatements $metaStatements
     */
    public function __construct(
        Date $date,
        RouterInterface $router,
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

    /**
     * @inheritdoc
     */
    public function fetchSitemapUrls($isSecure = null)
    {
        $this->addUrl('news/index/index', null, $isSecure);

        foreach ($this->newsRepository->getAll($this->date->getCurrentDateTime()) as $result) {
            $this->addUrl(
                sprintf(Helpers::URL_KEY_PATTERN, $result['id']),
                $this->date->format($result['updated_at'], 'Y-m-d'),
                $isSecure
            );
        }
    }
}
