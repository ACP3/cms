<?php
namespace ACP3\Core;

use ACP3\Core\Exceptions\ResultNotExists;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Modules\ControllerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class FrontController
 * @package ACP3\Core
 */
class FrontController
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
     * @param bool   $resolveParameters
     *
     * @throws \ACP3\Core\Exceptions\ControllerActionNotFound
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function dispatch($serviceId = '', $action = '', array $arguments = [], $resolveParameters = true)
    {
        $request = $this->container->get('core.request');

        $this->_checkForUriAlias($request);

        if (empty($serviceId)) {
            $serviceId = $request->getModule() . '.controller.' . $request->getArea() . '.' . $request->getController();
        }

        if ($this->container->has($serviceId)) {
            /** @var Modules\Controller $controller */
            $controller = $this->container->get($serviceId);

            if (empty($action)) {
                $action = $request->getControllerAction();
            }

            $action = 'action' . str_replace('_', '', $action);

            if (method_exists($controller, $action) === true) {
                $controller->setContainer($this->container);
                $controller->preDispatch();

                if (!empty($arguments)) {
                    $this->callControllerActionWithArguments($controller, $action, $arguments);
                } else {
                    $this->callControllerAction($request, $controller, $action, $resolveParameters);
                }

                $controller->display();
            } else {
                throw new Exceptions\ControllerActionNotFound('Controller action ' . get_class($controller) . '::' . $action . '() was not found!');
            }
        } else {
            throw new Exceptions\ControllerActionNotFound('Service-Id ' . $serviceId . ' was not found!');
        }
    }

    /**
     * Checks, whether there is an URI alias available for the current request.
     * If so, set the alias as the canonical URI
     *
     * @param \ACP3\Core\Http\RequestInterface $request
     */
    protected function _checkForUriAlias(RequestInterface $request)
    {
        // Return early, if we are currently in the admin panel
        if ($request->getArea() !== 'admin') {
            $routerAliases = $this->container->get('core.router.aliases');

            // If there is an URI alias available, set the alias as the canonical URI
            if ($routerAliases->uriAliasExists($request->getQuery()) === true &&
                $request->getOriginalQuery() !== $routerAliases->getUriAlias($request->getQuery()) . '/'
            ) {
                $this->container->get('core.seo')->setCanonicalUri(
                    $this->container->get('core.router')->route($request->getQuery())
                );
            }
        }
    }

    /**
     * @param \ACP3\Core\Http\RequestInterface       $request
     * @param \ACP3\Core\Modules\ControllerInterface $controller
     * @param string                                 $action
     * @param bool                                   $resolveParameters
     *
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    private function callControllerAction(RequestInterface $request, ControllerInterface $controller, $action, $resolveParameters)
    {
        $reflection = new \ReflectionMethod($controller, $action);
        $parameterCount = $reflection->getNumberOfParameters();

        if ($parameterCount === 0 || $resolveParameters === false) {
            $controller->$action();
            return;
        }

        $arguments = [];
        foreach ($reflection->getParameters() as $parameter) {
            if ($request->getPost()->has($parameter->getName())) {
                $arguments[$parameter->getPosition()] = $request->getPost()->get($parameter->getName());
            } elseif ($request->getParameters()->has($parameter->getName())) {
                $arguments[$parameter->getPosition()] = $request->getParameters()->get($parameter->getName());
            } elseif ($parameter->isOptional()) {
                $arguments[$parameter->getPosition()] = $parameter->getDefaultValue();
            }
        }

        if ($reflection->getNumberOfRequiredParameters() > count($arguments)) {
            throw new ResultNotExists();
        }

        $this->callControllerActionWithArguments($controller, $action, $arguments);
    }

    /**
     * @param \ACP3\Core\Modules\ControllerInterface $controller
     * @param string                                 $action
     * @param array                                  $arguments
     */
    private function callControllerActionWithArguments(ControllerInterface $controller, $action, $arguments)
    {
        call_user_func_array([$controller, $action], $arguments);
    }
}
