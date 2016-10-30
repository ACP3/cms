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
use ACP3\Modules\ACP3\Seo\Extension\SitemapAvailabilityExtensionInterface;
use ACP3\Modules\ACP3\Seo\Helper\MetaStatements;
use Thepixeldeveloper\Sitemap\Url;

class SitemapAvailabilityExtension implements SitemapAvailabilityExtensionInterface
{
    /**
     * @var Date
     */
    protected $date;
    /**
     * @var Router
     */
    protected $router;
    /**
     * @var ArticleRepository
     */
    protected $articleRepository;
    /**
     * @var MetaStatements
     */
    protected $metaStatements;

    /**
     * SitemapAvailability constructor.
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
        $this->date = $date;
        $this->router = $router;
        $this->articleRepository = $articleRepository;
        $this->metaStatements = $metaStatements;
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
        $articles = $this->articleRepository->getAll($this->date->getCurrentDateTime());

        $feedItems = [];
        foreach ($articles as $article) {
            $articleUrl = 'articles/index/details/id_' . $article['id'];

            if ($this->pageIsIndexable($articleUrl)) {
                $feedItems[] = (new Url($this->router->route($articleUrl, true)))->setLastMod($article['start']);
            }
        }

        return $feedItems;
    }

    /**
     * @param string $path
     * @return bool
     */
    private function pageIsIndexable($path)
    {
        return in_array($this->metaStatements->getRobotsSetting($path), ['index,follow', 'index,nofollow']);
    }
}
