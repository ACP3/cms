<?php
namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core\ACL;
use ACP3\Core\Application\ControllerResolver;

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
     * @var \ACP3\Core\Application\ControllerResolver
     */
    protected $frontController;

    /**
     * LoadModule constructor.
     *
     * @param \ACP3\Core\ACL $acl
     * @param \ACP3\Core\Application\ControllerResolver $frontController
     */
    public function __construct(ACL $acl, ControllerResolver $frontController)
    {
        $this->acl = $acl;
        $this->frontController = $frontController;
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
        if ($this->acl->hasPermission($path) === true) {
            $serviceId = strtolower($pathArray[1] . '.controller.' . $pathArray[0] . '.' . $pathArray[2] . '.' . $pathArray[3]);
            return $this->frontController->dispatch($serviceId, isset($params['args']) ? $params['args'] : []);
        }

        return '';
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
}
