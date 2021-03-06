<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Core\SEO;

use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Modules;
use ACP3\Core\Router\RouterInterface;
use ACP3\Core\SEO\MetaStatementsService as CoreMetaStatementsService;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Seo\Installer\Schema;
use ACP3\Modules\ACP3\Seo\Services\SeoInformationService;

class MetaStatementsService extends CoreMetaStatementsService
{
    /**
     * @var \ACP3\Core\Settings\SettingsInterface
     */
    private $config;
    /**
     * @var \ACP3\Core\Modules
     */
    private $modules;

    /**
     * @var array|null
     */
    private $aliasesCache;
    /**
     * @var SeoInformationService
     */
    private $seoInformationService;

    public function __construct(
        RequestInterface $request,
        RouterInterface $router,
        Modules $modules,
        SeoInformationService $seoInformationService,
        SettingsInterface $config
    ) {
        parent::__construct($request, $router);

        $this->config = $config;
        $this->modules = $modules;
        $this->seoInformationService = $seoInformationService;
    }

    protected function getSettings(): array
    {
        if (!$this->modules->isInstalled(Schema::MODULE_NAME)) {
            return parent::getSettings();
        }

        return $this->config->getSettings(Schema::MODULE_NAME);
    }

    public function getSeoInformation(string $path, string $key, string $defaultValue = ''): string
    {
        if (!$this->modules->isInstalled(Schema::MODULE_NAME)) {
            return parent::getSeoInformation($path, $key, $defaultValue);
        }

        // Lazy load the cache
        if ($this->aliasesCache === null) {
            $this->aliasesCache = $this->seoInformationService->getAllSeoInformation();
        }

        $path .= !preg_match('/\/$/', $path) ? '/' : '';

        return $this->aliasesCache[$path][$key] ?? parent::getSeoInformation($path, $key, $defaultValue);
    }
}
