<?php
namespace ACP3\Installer\Core;

use ACP3\Core\Exceptions;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class FrontController
 * @package ACP3\Installer\Core
 */
class FrontController extends \ACP3\Core\FrontController
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $serviceId
     * @param string $action
     * @param array  $arguments
     *
     * @throws \ACP3\Core\Exceptions\ControllerActionNotFound
     */
    public function dispatch($serviceId = '', $action = '', array $arguments = [])
    {
        $request = $this->container->get('core.request');

        if (empty($serviceId)) {
            $serviceId = $request->mod . '.controller.' . $request->area . '.' . $request->controller;
        }

        if ($this->container->has($serviceId)) {
            /** @var Modules\Controller $controller */
            $controller = $this->container->get($serviceId);

            if (empty($action)) {
                $action = $request->file;
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
