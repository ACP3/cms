<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Extension;

use ACP3\Core\Router\RouterInterface;
use ACP3\Modules\ACP3\Seo\Helper\MetaStatements;
use Thepixeldeveloper\Sitemap\Url;

abstract class AbstractSitemapAvailabilityExtension implements SitemapAvailabilityExtensionInterface
{
    /**
     * @var MetaStatements
     */
    private $metaStatements;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var Url[]
     */
    private $urls = [];

    /**
     * AbstractSitemapAvailabilityExtension constructor.
     * @param RouterInterface $router
     * @param MetaStatements $metaStatements
     */
    public function __construct(
        RouterInterface $router,
        MetaStatements $metaStatements
    ) {
        $this->router = $router;
        $this->metaStatements = $metaStatements;
    }

    /**
     * @param $routeName
     * @param null|string $lastModificationDate
     * @param bool|null $isSecure
     * @return $this
     */
    protected function addUrl($routeName, $lastModificationDate = null, $isSecure = null)
    {
        if ($this->pageIsIndexable($routeName)) {
            $this->urls[] = (new Url($this->router->route($routeName, true, $isSecure)))
                ->setLastMod($lastModificationDate);
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
     * @inheritdoc
     */
    public function getUrls($isSecure = null)
    {
        $this->urls = [];

        $this->fetchSitemapUrls($isSecure);

        return $this->urls;
    }

    /**
     * @param bool|null $isSecure
     * @return void
     */
    abstract protected function fetchSitemapUrls($isSecure = null);
}
