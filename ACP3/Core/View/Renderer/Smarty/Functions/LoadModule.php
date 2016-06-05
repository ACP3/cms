<?php
namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core\ACL;
use ACP3\Core\Application\ControllerActionDispatcher;
use Symfony\Component\HttpFoundation\Response;

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
     * @var \ACP3\Core\Application\ControllerActionDispatcher
     */
    protected $controllerActionDispatcher;

    /**
     * LoadModule constructor.
     *
     * @param \ACP3\Core\ACL $acl
     * @param \ACP3\Core\Application\ControllerActionDispatcher $controllerActionDispatcher
     */
    public function __construct(ACL $acl, ControllerActionDispatcher $controllerActionDispatcher)
    {
        $this->acl = $acl;
        $this->controllerActionDispatcher = $controllerActionDispatcher;
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
        $response = '';
        $pathArray = $this->convertPathToArray($params['module']);
        $path = $pathArray[0] . '/' . $pathArray[1] . '/' . $pathArray[2] . '/' . $pathArray[3];
        if ($this->acl->hasPermission($path) === true) {
            $serviceId = strtolower($pathArray[1] . '.controller.' . $pathArray[0] . '.' . $pathArray[2] . '.' . $pathArray[3]);
            $response =  $this->controllerActionDispatcher->dispatch(
                $serviceId,
                isset($params['args']) ? $params['args'] : []
            );

            if ($response instanceof Response) {
                $response = $response->getContent();
            }
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
}
