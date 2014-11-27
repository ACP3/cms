<?php
namespace ACP3\Installer\Core\View\Renderer\Smarty\Functions;

use ACP3\Core\View\Renderer\Smarty\Functions\AbstractFunction;

/**
 * Class Router
 * @package ACP3\Installer\Core\View\Renderer\Smarty\Functions
 */
class Router extends AbstractFunction
{
    /**
     * @var \ACP3\Installer\Core\Router
     */
    protected $router;

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
