<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Extension;

use ACP3\Core\Router\RouterInterface;
use ACP3\Core\SEO\MetaStatementsServiceInterface;
use Thepixeldeveloper\Sitemap\Url;

abstract class AbstractSitemapAvailabilityExtension implements SitemapAvailabilityExtensionInterface
{
    /**
     * @var Url[]
     */
    private $urls = [];

    public function __construct(private RouterInterface $router, private MetaStatementsServiceInterface $metaStatements)
    {
    }

    /**
     * @return $this
     */
    protected function addUrl(string $routeName, ?\DateTimeInterface $lastModificationDate = null, ?bool $isSecure = null)
    {
        if ($this->pageIsIndexable($routeName)) {
            $url = new Url($this->router->route($routeName, true, $isSecure));
            if ($lastModificationDate !== null) {
                $url->setLastMod($lastModificationDate);
            }

            $this->urls[] = $url;
        }

        return $this;
    }

    private function pageIsIndexable(string $routeName): bool
    {
        return \in_array($this->metaStatements->getRobotsSetting($routeName), [MetaStatementsServiceInterface::INDEX_FOLLOW, MetaStatementsServiceInterface::INDEX_NOFOLLOW], true);
    }

    /**
     * {@inheritdoc}
     */
    public function getUrls($isSecure = null)
    {
        $this->urls = [];

        $this->fetchSitemapUrls($isSecure);

        return $this->urls;
    }

    /**
     * @param bool|null $isSecure
     */
    abstract protected function fetchSitemapUrls($isSecure = null);
}
