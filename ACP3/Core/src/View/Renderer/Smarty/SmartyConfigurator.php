<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty;

use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Environment\ThemePathInterface;

class SmartyConfigurator
{
    public function __construct(private readonly ApplicationPath $appPath, private readonly ThemePathInterface $themePath, private readonly ApplicationMode $environment)
    {
    }

    public function configure(\Smarty $smarty): void
    {
        $smarty->setErrorReporting($this->isProduction() ? 0 : E_ALL);
        $smarty->setCompileId($this->themePath->getCurrentTheme());
        $smarty->setCompileCheck($this->isProduction() ? \Smarty::COMPILECHECK_OFF : \Smarty::COMPILECHECK_ON);
        $smarty->setCompileDir($this->appPath->getCacheDir() . 'tpl_compiled/');
        $smarty->setCacheDir($this->appPath->getCacheDir() . 'tpl_cached/');
    }

    private function isProduction(): bool
    {
        return $this->environment === ApplicationMode::PRODUCTION;
    }
}
