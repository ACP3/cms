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
     * @param string $routeName
     * @return bool
     */
    protected function pageIsIndexable($routeName)
    {
        return in_array($this->metaStatements->getRobotsSetting($routeName), ['index,follow', 'index,nofollow']);
    }

    /**
     * @param $routeName
     * @param null|string $lastModificationDate
     * @return Url
     */
    protected function instantiateSitemapUrl($routeName, $lastModificationDate = null)
    {
        return (new Url($this->router->route($routeName, true)))->setLastMod($lastModificationDate);
    }
}
