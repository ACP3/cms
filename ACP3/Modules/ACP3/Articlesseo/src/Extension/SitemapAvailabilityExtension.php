<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articlesseo\Extension;

use ACP3\Core\Date;
use ACP3\Core\Router\RouterInterface;
use ACP3\Core\SEO\MetaStatementsServiceInterface;
use ACP3\Modules\ACP3\Articles\Helpers;
use ACP3\Modules\ACP3\Articles\Installer\Schema;
use ACP3\Modules\ACP3\Articles\Model\Repository\ArticleRepository;
use ACP3\Modules\ACP3\Seo\Extension\AbstractSitemapAvailabilityExtension;

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

    public function __construct(
        Date $date,
        RouterInterface $router,
        ArticleRepository $articleRepository,
        MetaStatementsServiceInterface $metaStatements
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

    /**
     * {@inheritdoc}
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function fetchSitemapUrls($isSecure = null)
    {
        $this->addUrl('articles/index/index', null, $isSecure);

        foreach ($this->articleRepository->getAll($this->date->getCurrentDateTime()) as $result) {
            $this->addUrl(
                sprintf(Helpers::URL_KEY_PATTERN, $result['id']),
                $this->date->toDateTime($result['updated_at']),
                $isSecure
            );
        }
    }
}
