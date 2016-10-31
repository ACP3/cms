<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Extension;


use ACP3\Core\Router\Router;
use ACP3\Modules\ACP3\Seo\Helper\MetaStatements;
use Thepixeldeveloper\Sitemap\Url;

abstract class AbstractSitemapAvailabilityExtension implements SitemapAvailabilityExtensionInterface
{
    /**
     * @var MetaStatements
     */
    private $metaStatements;
    /**
     * @var Router
     */
    private $router;
    /**
     * @var Url[]
     */
    private $urls = [];

    /**
     * AbstractSitemapAvailabilityExtension constructor.
     * @param Router $router
     * @param MetaStatements $metaStatements
     */
    public function __construct(
        Router $router,
        MetaStatements $metaStatements)
    {
        $this->router = $router;
        $this->metaStatements = $metaStatements;
    }

    /**
     * @param $routeName
     * @param null|string $lastModificationDate
     * @return $this
     */
    protected function addUrl($routeName, $lastModificationDate = null)
    {
        if ($this->pageIsIndexable($routeName)) {
            $this->urls[] = (new Url($this->router->route($routeName, true)))->setLastMod($lastModificationDate);
        }

        return $this;
    }

    /**
     * @param string $routeName
     * @return bool
     */
    private function pageIsIndexable($routeName)
    {
        return in_array($this->metaStatements->getRobotsSetting($routeName), ['index,follow', 'index,nofollow']);
    }

    /**
     * @return \Thepixeldeveloper\Sitemap\Url[]
     */
    public function getUrls()
    {
        $this->fetchSitemapUrls();

        return $this->urls;
    }

    abstract protected function fetchSitemapUrls();
}
