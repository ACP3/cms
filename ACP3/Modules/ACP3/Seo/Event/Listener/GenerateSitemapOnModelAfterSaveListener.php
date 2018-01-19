<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Event\Listener;

use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Seo\Exception\SitemapGenerationException;
use ACP3\Modules\ACP3\Seo\Installer\Schema;
use ACP3\Modules\ACP3\Seo\Model\SitemapGenerationModel;
use ACP3\Modules\ACP3\Seo\Utility\SitemapAvailabilityRegistrar;
use Psr\Log\LoggerInterface;

class GenerateSitemapOnModelAfterSaveListener
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var SettingsInterface
     */
    private $settings;
    /**
     * @var SitemapAvailabilityRegistrar
     */
    private $sitemapRegistrar;
    /**
     * @var SitemapGenerationModel
     */
    private $sitemapGenerationModel;

    /**
     * GenerateSitemapOnModelAfterSaveListener constructor.
     *
     * @param LoggerInterface              $logger
     * @param SettingsInterface            $settings
     * @param SitemapAvailabilityRegistrar $sitemapRegistrar
     * @param SitemapGenerationModel       $sitemapGenerationModel
     */
    public function __construct(
        LoggerInterface $logger,
        SettingsInterface $settings,
        SitemapAvailabilityRegistrar $sitemapRegistrar,
        SitemapGenerationModel $sitemapGenerationModel
    ) {
        $this->logger = $logger;
        $this->settings = $settings;
        $this->sitemapRegistrar = $sitemapRegistrar;
        $this->sitemapGenerationModel = $sitemapGenerationModel;
    }

    /**
     * @param ModelSaveEvent $event
     */
    public function generateSeoSitemap(ModelSaveEvent $event)
    {
        if ($this->canGenerateSitemapAutomatically() && $this->isAllowedModule($event->getModuleName())) {
            try {
                $this->sitemapGenerationModel->save();
            } catch (SitemapGenerationException $e) {
                $this->logger->info($e->getMessage());
            }
        }
    }

    /**
     * @return bool
     */
    private function canGenerateSitemapAutomatically()
    {
        $seoSettings = $this->settings->getSettings(Schema::MODULE_NAME);

        return $seoSettings['sitemap_is_enabled'] == 1 && $seoSettings['sitemap_save_mode'] == 1;
    }

    /**
     * @param string $moduleName
     *
     * @return bool
     */
    private function isAllowedModule($moduleName)
    {
        return \array_key_exists($moduleName, $this->sitemapRegistrar->getAvailableModules());
    }
}
