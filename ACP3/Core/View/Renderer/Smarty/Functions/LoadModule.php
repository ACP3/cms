<?php
namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core\ACL;
use ACP3\Core\Application\FrontController;

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
     * @var \ACP3\Core\Application\FrontController
     */
    protected $frontController;

    /**
     * LoadModule constructor.
     *
     * @param \ACP3\Core\ACL                         $acl
     * @param \ACP3\Core\Application\FrontController $frontController
     */
    public function __construct(ACL $acl, FrontController $frontController)
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
        $pathArray = explode('/', strtolower($params['module']));

        if (empty($pathArray[2]) === true) {
            $pathArray[2] = 'index';
        }
        if (empty($pathArray[3]) === true) {
            $pathArray[3] = 'index';
        }

        $path = $pathArray[0] . '/' . $pathArray[1] . '/' . $pathArray[2] . '/' . $pathArray[3];

        if ($this->acl->hasPermission($path)) {
            $serviceId = strtolower($pathArray[1] . '.controller.' . $pathArray[0] . '.' . $pathArray[2] . '.' . $pathArray[3]);

            $this->frontController->dispatch($serviceId, isset($params['args']) ? $params['args'] : []);
        }
    }
}
