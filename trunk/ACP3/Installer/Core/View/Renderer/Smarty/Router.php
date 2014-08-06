<?php
namespace ACP3\Installer\Core\View\Renderer\Smarty;

use ACP3\Core\View\Renderer\Smarty\AbstractPlugin;

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

    public function __construct(\ACP3\Installer\Core\Router $router)
    {
        $this->router = $router;
    }

    /**
     * @param $params
     * @return string
     */
    public function process($params)
    {
        return $this->router->route(!empty($params['args']) ? $params['args'] : '');
    }
}