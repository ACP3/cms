<?php
namespace ACP3\Core\View\Renderer\Smarty\Plugins;

use ACP3\Core;

/**
 * Class URI
 * @package ACP3\Core\View\Renderer\Smarty
 */
class Router extends AbstractPlugin
{
    /**
     * @var Core\Router
     */
    protected $router;
    /**
     * @var string
     */
    protected $pluginName = 'uri';

    public function __construct(Core\Router $router)
    {
        $this->router = $router;
    }

    /**
     * @param array $params
     * @return mixed|string
     */
    public function process(array $params)
    {
        return $this->router->route(!empty($params['args']) ? $params['args'] : '');
    }
}