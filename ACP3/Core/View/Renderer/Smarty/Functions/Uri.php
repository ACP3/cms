<?php
namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core;

/**
 * Class Uri
 * @package ACP3\Core\View\Renderer\Smarty\Functions
 */
class Uri extends AbstractFunction
{
    /**
     * @var \ACP3\Core\Router
     */
    protected $router;

    /**
     * @param \ACP3\Core\Router $router
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
        return $this->router->route(
            !empty($params['args']) ? $params['args'] : '',
            isset($params['absolute']) ? (bool)$params['absolute'] : false,
            isset($params['secure']) ? (bool)$params['secure'] : false
        );
    }
}
