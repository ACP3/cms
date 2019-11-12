<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Model;

use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Seo\Exception\SitemapGenerationException;
use ACP3\Modules\ACP3\Seo\Installer\Schema;
use ACP3\Modules\ACP3\Seo\Utility\SitemapAvailabilityRegistrar;
use Thepixeldeveloper\Sitemap\Interfaces\DriverInterface;
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
     * @var SettingsInterface
     */
    private $settings;
    /**
     * @var array
     */
    private $filenameMap = [
        0 => [
            ['filename' => 'sitemap.xml', 'secure' => null],
        ],
        1 => [
            ['filename' => 'sitemap_https.xml', 'secure' => true],
            ['filename' => 'sitemap_http.xml', 'secure' => false],
        ],
    ];
    /**
     * @var DriverInterface
     */
    private $xmlSitemapDriver;

    /**
     * SitemapGenerationModel constructor.
     */
    public function __construct(
        ApplicationPath $applicationPath,
        SettingsInterface $settings,
        SitemapAvailabilityRegistrar $sitemapRegistrar,
        DriverInterface $xmlSitemapDriver
    ) {
        $this->applicationPath = $applicationPath;
        $this->sitemapRegistrar = $sitemapRegistrar;
        $this->settings = $settings;
        $this->xmlSitemapDriver = $xmlSitemapDriver;
    }

    /**
     * @return bool
     *
     * @throws \ACP3\Modules\ACP3\Seo\Exception\SitemapGenerationException
     */
    public function save()
    {
        $separateSitemaps = $this->settings->getSettings(Schema::MODULE_NAME)['sitemap_separate'];

        foreach ($this->filenameMap[$separateSitemaps] as $sitemap) {
            $this->checkSitemapFilePermissions($sitemap['filename']);

            $urlSet = $this->collectSitemapItems($sitemap['secure']);

            $this->saveSitemap($urlSet, $sitemap['filename']);
        }

        return true;
    }

    /**
     * @throws SitemapGenerationException
     */
    protected function checkSitemapFilePermissions(string $filename)
    {
        $filePath = $this->getSitemapFilePath($filename);

        if (!\is_file($filePath)) {
            \touch($filePath);
        }

        if (!\is_file($filePath) || !\is_writable($filePath)) {
            throw new SitemapGenerationException('The requested file "' . $filePath . '" either not exists or is not writable.' . 'Aborting sitemap generation.');
        }
    }

    /**
     * @return string
     */
    protected function getSitemapFilePath(string $filename)
    {
        return ACP3_ROOT_DIR . '/' . $filename;
    }

    /**
     * @return Urlset
     */
    protected function collectSitemapItems(?bool $isSecure)
    {
        $urlSet = new Urlset();
        foreach ($this->sitemapRegistrar->getAvailableModules() as $module) {
            foreach ($module->getUrls($isSecure) as $sitemapItem) {
                $urlSet->add($sitemapItem);
            }
        }

        return $urlSet;
    }

    /**
     * @return bool
     */
    protected function saveSitemap(Urlset $urlSet, string $filename)
    {
        $urlSet->accept($this->xmlSitemapDriver);

        return \file_put_contents($this->getSitemapFilePath($filename), $this->xmlSitemapDriver->output()) !== false;
    }
}
