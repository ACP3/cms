<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Articles\Extension;


use ACP3\Core\Date;
use ACP3\Core\Router\Router;
use ACP3\Modules\ACP3\Articles\Installer\Schema;
use ACP3\Modules\ACP3\Articles\Model\Repository\ArticleRepository;
use ACP3\Modules\ACP3\Seo\Extension\AbstractSitemapAvailabilityExtension;
use ACP3\Modules\ACP3\Seo\Helper\MetaStatements;

class SitemapAvailabilityExtension extends AbstractSitemapAvailabilityExtension
{
    /**
     * @var Date
     */
    protected $date;
    /**
     * @var ArticleRepository
     */
    protected $articleRepository;

    /**
     * SitemapAvailabilityExtension constructor.
     * @param Date $date
     * @param Router $router
     * @param ArticleRepository $articleRepository
     * @param MetaStatements $metaStatements
     */
    public function __construct(
        Date $date,
        Router $router,
        ArticleRepository $articleRepository,
        MetaStatements $metaStatements
    ) {
        parent::__construct($router, $metaStatements);

        $this->date = $date;
        $this->articleRepository = $articleRepository;
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
        $this->addUrl('articles/index/index');

        foreach ($this->articleRepository->getAll($this->date->getCurrentDateTime()) as $result) {
            $this->addUrl('articles/index/details/id_' . $result['id'], $result['updated_at']);
        }
    }
}
