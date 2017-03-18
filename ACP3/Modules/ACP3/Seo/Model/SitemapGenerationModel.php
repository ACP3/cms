<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Model;

use ACP3\Core\Environment\ApplicationPath;
use ACP3\Modules\ACP3\Seo\Exception\SitemapGenerationException;
use ACP3\Modules\ACP3\Seo\Utility\SitemapAvailabilityRegistrar;
use Thepixeldeveloper\Sitemap\Output;
use Thepixeldeveloper\Sitemap\Urlset;

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
        SitemapAvailabilityRegistrar $sitemapRegistrar
    ) {
        $this->applicationPath = $applicationPath;
        $this->sitemapRegistrar = $sitemapRegistrar;
    }

    /**
     * @return bool
     */
    public function save()
    {
        $this->checkSitemapFilePermissions();

        $urlSet = $this->collectSitemapItems();

        return $this->saveSitemap($urlSet);
    }

    /**
     * @throws SitemapGenerationException
     */
    protected function checkSitemapFilePermissions()
    {
        $filePath = $this->getSitemapFilePath();

        if (!is_file($filePath)) {
            touch($filePath);
        }

        if (!is_file($filePath) || !is_writable($filePath)) {
            throw new SitemapGenerationException(
                'The requested file "' . $filePath . '" either not exists or is not writable.'
                . 'Aborting sitemap generation.'
            );
        }
    }

    /**
     * @return string
     */
    protected function getSitemapFilePath()
    {
        return ACP3_ROOT_DIR . $this->fileName;
    }

    /**
     * @return Urlset
     */
    protected function collectSitemapItems()
    {
        $urlSet = new Urlset();
        foreach ($this->sitemapRegistrar->getAvailableModules() as $module) {
            foreach ($module->getUrls() as $sitemapItem) {
                $urlSet->addUrl($sitemapItem);
            }
        }
        return $urlSet;
    }

    /**
     * @param Urlset $urlSet
     * @return bool
     */
    protected function saveSitemap(Urlset $urlSet)
    {
        $output = (new Output())->getOutput($urlSet);

        return file_put_contents($this->getSitemapFilePath(), $output) !== false;
    }
}
