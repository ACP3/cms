<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\EventListener;

use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Core\Modules;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Seo\Exception\SitemapGenerationException;
use ACP3\Modules\ACP3\Seo\Installer\Schema;
use ACP3\Modules\ACP3\Seo\Model\SitemapGenerationModel;
use ACP3\Modules\ACP3\Seo\Utility\SitemapAvailabilityRegistrar;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class GenerateSitemapListener implements EventSubscriberInterface
{
    private bool $scheduleSitemapRebuild = false;

    public function __construct(private readonly LoggerInterface $logger, private readonly Modules $modules, private readonly SettingsInterface $settings, private readonly SitemapAvailabilityRegistrar $sitemapRegistrar, private readonly SitemapGenerationModel $sitemapGenerationModel, private readonly RequestStack $requestStack)
    {
    }

    public function onModelAfterSave(ModelSaveEvent $event): void
    {
        if (!$this->modules->isInstalled(Schema::MODULE_NAME)) {
            return;
        }

        if ($this->canGenerateSitemapAutomatically() && $this->isAllowedModule($event->getModuleName())) {
            $this->scheduleSitemapRebuild = true;
        }
    }

    private function canGenerateSitemapAutomatically(): bool
    {
        $seoSettings = $this->settings->getSettings(Schema::MODULE_NAME);

        return $seoSettings['sitemap_is_enabled'] == 1 && $seoSettings['sitemap_save_mode'] == 1;
    }

    private function isAllowedModule(string $moduleName): bool
    {
        return \array_key_exists($moduleName, $this->sitemapRegistrar->getAvailableModules());
    }

    public function onKernelTerminate(TerminateEvent $event): void
    {
        if (!$this->scheduleSitemapRebuild) {
            return;
        }

        $this->requestStack->push($event->getRequest());

        try {
            $this->sitemapGenerationModel->save();
        } catch (SitemapGenerationException $e) {
            $this->logger->warning($e->getMessage());
        }

        $this->requestStack->pop();
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'core.model.after_save' => ['onModelAfterSave', -255],
            KernelEvents::TERMINATE => 'onKernelTerminate',
        ];
    }
}
