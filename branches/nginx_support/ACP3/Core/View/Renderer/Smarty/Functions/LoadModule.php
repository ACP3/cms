<?php
namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core\FrontController;
use ACP3\Core\ACL;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class LoadModule
 * @package ACP3\Core\View\Renderer\Smarty\Functions
 */
class LoadModule extends AbstractFunction
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritdoc
     */
    public function getPluginName()
    {
        return 'load_module';
    }

    /**
     * @inheritdoc
     */
    public function process(array $params, \Smarty_Internal_Template $smarty)
    {
        $pathArray = explode('/', strtolower($params['module']));

        if (empty($pathArray[2]) === true) {
            $pathArray[2] = 'index';
        }
        if (empty($pathArray[3]) === true) {
            $pathArray[3] = 'index';
        }

        $path = $pathArray[0] . '/' . $pathArray[1] . '/' . $pathArray[2] . '/' . $pathArray[3];

        if ($this->container->get('core.acl')->hasPermission($path)) {
            $serviceId = strtolower($pathArray[1] . '.controller.' . $pathArray[0] . '.' . $pathArray[2]);

            $frontController = new FrontController($this->container);
            $frontController->dispatch($serviceId, $pathArray[3]);
        }
    }
}
