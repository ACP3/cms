<?php
namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core;

class Uri extends AbstractFunction
{
    /**
     * @var \ACP3\Core\Router\RouterInterface
     */
    protected $router;

    /**
     * Uri constructor.
     *
     * @param \ACP3\Core\Router\RouterInterface $router
     */
    public function __construct(Core\Router\RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @inheritdoc
     */
    public function getExtensionName()
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
            isset($params['secure']) ? (bool)$params['secure'] : null
        );
    }
}
