<?php
namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core\ACL;
use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Router;

/**
 * Class LoadModule
 * @package ACP3\Core\View\Renderer\Smarty\Functions
 */
class LoadModule extends AbstractFunction
{
    /**
     * @var \ACP3\Core\ACL
     */
    protected $acl;
    /**
     * @var Router
     */
    protected $router;
    /**
     * @var string
     */
    protected $applicationMode;

    /**
     * LoadModule constructor.
     *
     * @param \ACP3\Core\ACL $acl
     * @param Router $router
     * @param string $applicationMode
     */
    public function __construct(
        ACL $acl,
        Router $router,
        $applicationMode)
    {
        $this->acl = $acl;
        $this->router = $router;
        $this->applicationMode = $applicationMode;
    }

    /**
     * @inheritdoc
     */
    public function getExtensionName()
    {
        return 'load_module';
    }

    /**
     * @inheritdoc
     */
    public function process(array $params, \Smarty_Internal_Template $smarty)
    {
        $pathArray = $this->convertPathToArray($params['module']);
        $path = $pathArray[0] . '/' . $pathArray[1] . '/' . $pathArray[2] . '/' . $pathArray[3];
        $arguments = isset($params['args']) ? $params['args'] : [];

        $response = '';
        if ($this->acl->hasPermission($path) === true) {
            $response = $this->esiInclude($path, $arguments);
        }

        return $response;
    }

    /**
     * @param string $resource
     *
     * @return array
     */
    protected function convertPathToArray($resource)
    {
        $pathArray = explode('/', strtolower($resource));

        if (empty($pathArray[2]) === true) {
            $pathArray[2] = 'index';
        }
        if (empty($pathArray[3]) === true) {
            $pathArray[3] = 'index';
        }
        return $pathArray;
    }

    /**
     * @param string $path
     * @param array $arguments
     * @return string
     */
    protected function esiInclude($path, array $arguments)
    {
        $routeArguments = '';
        foreach ($arguments as $key => $value) {
            $routeArguments.= '/' . $key . '_' . $value;
        }

        $debug = '';
        if ($this->applicationMode === ApplicationMode::PRODUCTION) {
            $debug = ' onerror="continue"';
        }

        $esiTag = '<esi:include src="' . $this->router->route($path . $routeArguments, true) . '"' . $debug . ' />';

        return $esiTag;
    }
}
