<?php
namespace ACP3\Core\View\Renderer\Smarty;

use ACP3\Core\FrontController;
use ACP3\Core\Modules;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class LoadModule
 * @package ACP3\Core\View\Renderer\Smarty
 */
class LoadModule extends AbstractPlugin
{
    /**
     * @var Modules
     */
    protected $modules;
    /**
     * @var Container
     */
    protected $container;
    /**
     * @var string
     */
    protected $pluginName = 'load_module';

    public function __construct(Modules $modules)
    {
        $this->modules = $modules;
    }

    /**
     * @param Container $container
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param $params
     *
     * @throws \Exception
     * @return string
     */
    public function process($params)
    {
        $pathArray = explode('/', strtolower($params['module']));

        if (empty($pathArray[2]) === true) {
            $pathArray[2] = 'index';
        }
        if (empty($pathArray[3]) === true) {
            $pathArray[3] = 'index';
        }

        $path = $pathArray[0] . '/' . $pathArray[1] . '/' . $pathArray[2] . '/' . $pathArray[3];

        if ($this->modules->hasPermission($path)) {
            $serviceId = strtolower($pathArray[1] . '.controller.' . $pathArray[0] . '.' . $pathArray[2]);

            $frontController = new FrontController($this->container);
            $frontController->dispatch($serviceId, $pathArray[3]);
        }
    }
}