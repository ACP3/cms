<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\View\Renderer;

use ACP3\Core\Config;
use ACP3\Core\Environment\ApplicationPath;

/**
 * Class SmartyRendererFactory
 * @package ACP3\Core\View\Renderer
 */
class SmartyRendererFactory
{
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;
    /**
     * @var \ACP3\Core\Config
     */
    protected $config;
    /**
     * @var string
     */
    protected $environment;

    /**
     * SmartyRendererFactory constructor.
     *
     * @param \ACP3\Core\Environment\ApplicationPath $appPath
     * @param \ACP3\Core\Config                      $config
     * @param string                                 $environment
     */
    public function __construct(ApplicationPath $appPath, Config $config, $environment)
    {
        $this->appPath = $appPath;
        $this->config = $config;
        $this->environment = $environment;
    }

    /**
     * @return \ACP3\Core\View\Renderer\Smarty
     */
    public function create()
    {
        $renderer = new Smarty($this->appPath, $this->environment);
        $renderer->configure(['compile_id' => $this->config->getSettings('system')['design']]);
        return $renderer;
    }
}
