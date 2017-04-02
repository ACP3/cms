<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\View\Renderer;

use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Installer\Schema;

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
     * @var SettingsInterface
     */
    protected $config;
    /**
     * @var string
     */
    protected $environment;

    /**
     * SmartyRendererFactory constructor.
     * @param ApplicationPath $appPath
     * @param SettingsInterface $config
     * @param $environment
     */
    public function __construct(ApplicationPath $appPath, SettingsInterface $config, $environment)
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
        $smarty = new \Smarty();
        $renderer = new Smarty($smarty, $this->appPath, $this->environment);
        $renderer->configure(['compile_id' => $this->config->getSettings(Schema::MODULE_NAME)['design']]);
        return $renderer;
    }
}
