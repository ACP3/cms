<?php
namespace ACP3\Core\View\Renderer\Smarty;

use ACP3\Application;

/**
 * Class LoadModule
 * @package ACP3\Core\View\Renderer\Smarty
 */
class LoadModule extends AbstractPlugin
{
    /**
     * @var string
     */
    protected $pluginName = 'load_module';

    /**
     * @param $params
     * @throws \Exception
     * @return string
     */
    public function process($params)
    {
        $pathArray = array_map(function ($value) {
            return str_replace(' ', '', ucwords(str_replace('_', ' ', $value)));
        }, explode('/', $params['module']));

        if (empty($pathArray[2]) === true) {
            $pathArray[2] = 'Index';
        }
        if (empty($pathArray[3]) === true) {
            $pathArray[3] = 'Index';
        }

        if ($pathArray[0] !== 'Frontend') {
            $className = "\\ACP3\\Modules\\" . $pathArray[1] . "\\Controller\\" . $pathArray[0] . "\\" . $pathArray[2];
        } else {
            $className = "\\ACP3\\Modules\\" . $pathArray[1] . "\\Controller\\" . $pathArray[2];
        }

        Application::dispatch($className, $pathArray[3]);
    }
}