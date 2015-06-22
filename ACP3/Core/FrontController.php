<?php
namespace ACP3\Core;

use Symfony\Component\DependencyInjection\Container;

/**
 * Class FrontController
 * @package ACP3\Core
 */
class FrontController
{
    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    protected $container;

    /**
     * @param \Symfony\Component\DependencyInjection\Container $container
     */
    public function __construct(Container $container)
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

        $this->_checkForUriAlias($request);

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

                if (!empty($arguments)) {
                    call_user_func_array([$controller, $action], $arguments);
                } else {
                    $controller->$action();
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
     * @param \ACP3\Core\Request $request
     */
    private function _checkForUriAlias(Request $request)
    {
        // Return early, if we are currently in the admin panel
        if ($request->area !== 'admin') {
            $routerAliases = $this->container->get('core.router.aliases');

            // If there is an URI alias available, set the alias as the canonical URI
            if ($routerAliases->uriAliasExists($request->query) === true &&
                $request->originalQuery !== $routerAliases->getUriAlias($request->query) . '/'
            ) {
                $this->container->get('core.seo')->setCanonicalUri(
                    $this->container->get('core.router')->route($request->query)
                );
            }
        }
    }
}
