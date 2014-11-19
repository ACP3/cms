<?php
namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core;

/**
 * Class Router
 * @package ACP3\Core\View\Renderer\Smarty\Functions
 */
class Router extends AbstractFunction
{
    /**
     * @var Core\Router
     */
    protected $router;
    /**
     * @var string
     */
    protected $pluginName = 'uri';

    /**
     * @param Core\Router $router
     */
    public function __construct(Core\Router $router)
    {
        $this->router = $router;
    }

    /**
     * @inheritdoc
     */
    public function getPluginName()
    {
        return 'uri';
    }

    /**
     * @inheritdoc
     */
    public function process(array $params, \Smarty_Internal_Template $smarty)
    {
        return $this->router->route(!empty($params['args']) ? $params['args'] : '');
    }
}