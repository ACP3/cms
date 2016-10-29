<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Model;


use ACP3\Core\Environment\ApplicationPath;
use ACP3\Modules\ACP3\Seo\Utility\SitemapAvailabilityRegistrar;

class SitemapGenerationModel
{
    /**
     * @var ApplicationPath
     */
    private $applicationPath;
    /**
     * @var SitemapAvailabilityRegistrar
     */
    protected $sitemapRegistrar;
    /**
     * @var string
     */
    protected $fileName = 'sitemap.xml';

    /**
     * SitemapGenerationModel constructor.
     * @param ApplicationPath $applicationPath
     * @param SitemapAvailabilityRegistrar $sitemapRegistrar
     */
    public function __construct(
        ApplicationPath $applicationPath,
        SitemapAvailabilityRegistrar $sitemapRegistrar)
    {
        $this->applicationPath = $applicationPath;
        $this->sitemapRegistrar = $sitemapRegistrar;
    }

    public function save()
    {
        foreach ($this->sitemapRegistrar->getAvailableModules() as $module) {

        }
    }
}
