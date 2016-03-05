<?php

namespace ACP3\Core\View\Renderer;

use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Environment\ApplicationPath;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Renderer for the Smarty template engine
 *
 * @package ACP3\Core\View\Renderer
 */
class Smarty extends \Smarty implements RendererInterface
{
    use ContainerAwareTrait;

    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;

    /**
     * Smarty constructor.
     *
     * @param \ACP3\Core\Environment\ApplicationPath $appPath
     */
    public function __construct(ApplicationPath $appPath)
    {
        parent::__construct();

        $this->appPath = $appPath;
    }

    /**
     * @param array $params
     *
     * @throws \SmartyException
     */
    public function configure(array $params = [])
    {
        $settings = $this->container->get('core.config')->getSettings('system');

        $this->setErrorReporting($this->isDevOrInstall() ? E_ALL : 0);
        $this->setCompileId(!empty($params['compile_id']) ? $params['compile_id'] : $settings['design']);
        $this->setCompileCheck($this->isDevOrInstall());
        $this->setCompileDir($this->appPath->getCacheDir() . 'tpl_compiled/');
        $this->setCacheDir($this->appPath->getCacheDir() . 'tpl_cached/');
    }

    /**
     * @return bool
     */
    protected function isDevOrInstall()
    {
        return $this->container->getParameter('core.environment') === ApplicationMode::DEVELOPMENT ||
        $this->container->getParameter('core.environment') === ApplicationMode::INSTALLER ||
        $this->container->getParameter('core.environment') === ApplicationMode::UPDATER;
    }
}
