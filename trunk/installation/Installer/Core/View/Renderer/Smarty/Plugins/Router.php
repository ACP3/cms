<?php
namespace ACP3\Installer\Core\View\Renderer\Smarty\Plugins;

use ACP3\Core\View\Renderer\Smarty\Plugins\AbstractPlugin;

/**
 * Class Router
 * @package ACP3\Installer\Core\View\Renderer\Smarty
 */
class Router extends AbstractPlugin
{
    /**
     * @var \ACP3\Installer\Core\Router
     */
    protected $router;
    /**
     * @var string
     */
    protected $pluginName = 'uri';

    /**
     * @param \ACP3\Installer\Core\Router $router
     */
    public function __construct(\ACP3\Installer\Core\Router $router)
    {
        $this->router = $router;
    }

    /**
     * @inheritdoc
     */
    public function process(array $params, \Smarty_Internal_Template $smarty)
    {
        return $this->router->route(!empty($params['args']) ? $params['args'] : '');
    }
}