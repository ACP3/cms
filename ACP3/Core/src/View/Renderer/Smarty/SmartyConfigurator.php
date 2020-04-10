<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty;

use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Installer\Schema;

class SmartyConfigurator
{
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;
    /**
     * @var SettingsInterface
     */
    protected $config;
    /**
     * @var string
     */
    protected $environment;

    public function __construct(ApplicationPath $appPath, SettingsInterface $config, string $environment)
    {
        $this->appPath = $appPath;
        $this->config = $config;
        $this->environment = $environment;
    }

    public function configure(\Smarty $smarty): void
    {
        $smarty->setErrorReporting($this->isProduction() ? 0 : E_ALL);
        $smarty->setCompileId($this->config->getSettings(Schema::MODULE_NAME)['design']);
        $smarty->setCompileCheck(!$this->isProduction());
        $smarty->setCompileDir($this->appPath->getCacheDir() . 'tpl_compiled/');
        $smarty->setCacheDir($this->appPath->getCacheDir() . 'tpl_cached/');
    }

    private function isProduction(): bool
    {
        return $this->environment === ApplicationMode::PRODUCTION;
    }
}
