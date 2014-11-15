<?php
namespace ACP3\Core\View\Renderer\Smarty\Plugins;

use ACP3\Core\FrontController;
use ACP3\Core\ACL;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class LoadModule
 * @package ACP3\Core\View\Renderer\Smarty
 */
class LoadModule extends AbstractPlugin
{
    /**
     * @var Container
     */
    protected $container;
    /**
     * @var string
     */
    protected $pluginName = 'load_module';

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