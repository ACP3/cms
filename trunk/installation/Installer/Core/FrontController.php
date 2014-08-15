<?php
namespace ACP3\Installer\Core;

use ACP3\Core\Exceptions;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class FrontController
 * @package ACP3\Installer\Core
 */
class FrontController extends \ACP3\Core\FrontController
{
    /**
     * @var Request
     */
    protected $request;
    /**
     * @var Container
     */
    protected $container;


    public function __construct(Container $container)
    {
        $this->request = $container->get('core.request');
        $this->container = $container;
    }

    /**
     * @param $serviceId
     * @param $action
     * @throws Exceptions\ControllerActionNotFound
     */
    public function dispatch($serviceId = '', $action = '')
    {
        if (empty($serviceId)) {
            $serviceId = $this->request->mod . '.controller.' . $this->request->area . '.' . $this->request->controller;
        }

        if ($this->container->has($serviceId)) {
            /** @var Modules\Controller $controller */
            $controller = $this->container->get($serviceId);

            if (empty($action)) {
                $action = $this->request->file;
            }

            $action = 'action' . str_replace('_', '', $action);

            if (method_exists($controller, $action) === true) {
                $controller->setContainer($this->container);
                $controller->preDispatch();
                $controller->$action();
                $controller->display();
            } else {
                throw new Exceptions\ControllerActionNotFound('Controller action ' . get_class($controller) . '::' . $action . '() was not found!');
            }
        } else {
            throw new Exceptions\ControllerActionNotFound('Service-Id ' . $serviceId . ' was not found!');
        }
    }
} 