<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Installer\Core\View\Renderer\Smarty;

class SmartyConfigurator
{
    /**
     * @var string
     */
    protected $environment;

    /**
     * SmartyConfigurator constructor.
     *
     * @param string $environment
     */
    public function __construct(string $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @param \Smarty $smarty
     */
    public function configure(\Smarty $smarty)
    {
        $smarty->setErrorReporting(E_ALL);
        $smarty->setCompileId($this->environment);
        $smarty->setCompileCheck(true);
    }
}
