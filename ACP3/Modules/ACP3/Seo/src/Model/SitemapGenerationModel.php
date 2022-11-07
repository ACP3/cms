<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Model;

use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Seo\Exception\SitemapGenerationException;
use ACP3\Modules\ACP3\Seo\Installer\Schema;
use ACP3\Modules\ACP3\Seo\Utility\SitemapAvailabilityRegistrar;
use Thepixeldeveloper\Sitemap\Interfaces\DriverInterface;
use Thepixeldeveloper\Sitemap\Urlset;

class SitemapGenerationModel
{
    /**
     * @var array<int, array<int, array{filename: string, secure: bool|null}>>
     */
    private array $filenameMap = [
        0 => [
            ['filename' => 'sitemap.xml', 'secure' => null],
        ],
        1 => [
            ['filename' => 'sitemap_https.xml', 'secure' => true],
            ['filename' => 'sitemap_http.xml', 'secure' => false],
        ],
    ];

    public function __construct(private readonly SettingsInterface $settings, private readonly SitemapAvailabilityRegistrar $sitemapRegistrar, private readonly DriverInterface $xmlSitemapDriver)
    {
    }

    /**
     * @throws \ACP3\Modules\ACP3\Seo\Exception\SitemapGenerationException
     */
    public function save(): bool
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
    private function checkSitemapFilePermissions(string $filename): void
    {
        $filePath = $this->getSitemapFilePath($filename);

        if (!is_file($filePath)) {
            touch($filePath);
        }

        if (!is_file($filePath) || !is_writable($filePath)) {
            throw new SitemapGenerationException('The requested file "' . $filePath . '" either not exists or is not writable. Aborting sitemap generation.');
        }
    }

    private function getSitemapFilePath(string $filename): string
    {
        return ACP3_ROOT_DIR . '/' . $filename;
    }

    private function collectSitemapItems(?bool $isSecure): Urlset
    {
        $urlSet = new Urlset();
        foreach ($this->sitemapRegistrar->getAvailableModules() as $module) {
            foreach ($module->getUrls($isSecure) as $sitemapItem) {
                $urlSet->add($sitemapItem);
            }
        }

        return $urlSet;
    }

    private function saveSitemap(Urlset $urlSet, string $filename): bool
    {
        $urlSet->accept($this->xmlSitemapDriver);

        return file_put_contents($this->getSitemapFilePath($filename), $this->xmlSitemapDriver->output()) !== false;
    }
}
