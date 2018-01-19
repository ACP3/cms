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
     * SitemapGenerationModel constructor.
     *
     * @param ApplicationPath              $applicationPath
     * @param SettingsInterface            $settings
     * @param SitemapAvailabilityRegistrar $sitemapRegistrar
     */
    public function __construct(
        ApplicationPath $applicationPath,
        SettingsInterface $settings,
        SitemapAvailabilityRegistrar $sitemapRegistrar
    ) {
        $this->applicationPath = $applicationPath;
        $this->sitemapRegistrar = $sitemapRegistrar;
        $this->settings = $settings;
    }

    /**
     * @return bool
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
     * @param string $filename
     *
     * @throws SitemapGenerationException
     */
    protected function checkSitemapFilePermissions($filename)
    {
        $filePath = $this->getSitemapFilePath($filename);

        if (!\is_file($filePath)) {
            \touch($filePath);
        }

        if (!\is_file($filePath) || !\is_writable($filePath)) {
            throw new SitemapGenerationException(
                'The requested file "' . $filePath . '" either not exists or is not writable.'
                . 'Aborting sitemap generation.'
            );
        }
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    protected function getSitemapFilePath($filename)
    {
        return ACP3_ROOT_DIR . $filename;
    }

    /**
     * @param bool|null $isSecure
     *
     * @return Urlset
     */
    protected function collectSitemapItems($isSecure)
    {
        $urlSet = new Urlset();
        foreach ($this->sitemapRegistrar->getAvailableModules() as $module) {
            foreach ($module->getUrls($isSecure) as $sitemapItem) {
                $urlSet->addUrl($sitemapItem);
            }
        }

        return $urlSet;
    }

    /**
     * @param Urlset $urlSet
     * @param string $filename
     *
     * @return bool
     */
    protected function saveSitemap(Urlset $urlSet, $filename)
    {
        $output = (new Output())->getOutput($urlSet);

        return \file_put_contents($this->getSitemapFilePath($filename), $output) !== false;
    }
}
