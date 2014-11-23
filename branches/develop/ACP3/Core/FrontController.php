<?php
namespace ACP3\Core;

use Symfony\Component\DependencyInjection\Container;

class FrontController
{
    /**
     * @var Request
     */
    protected $request;
    /**
     * @var Redirect
     */
    protected $redirect;
    /**
     * @var Router\Aliases
     */
    protected $routerAliases;
    /**
     * @var Container
     */
    protected $container;


    public function __construct(Container $container)
    {
        $this->request = $container->get('core.request');
        $this->redirect = $container->get('core.redirect');
        $this->routerAliases = $container->get('core.router.aliases');
        $this->container = $container;
    }

    /**
     * @param $serviceId
     * @param $action
     *
     * @throws Exceptions\ControllerActionNotFound
     */
    public function dispatch($serviceId = '', $action = '')
    {
        $this->_checkForUriAlias();

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

    /**
     * Überprüft die URI auf einen möglichen URI-Alias und
     * macht im Erfolgsfall einen Redirect darauf
     *
     * @return void
     */
    private function _checkForUriAlias()
    {
        // Nur ausführen, falls URI-Aliase aktiviert sind
        if ($this->request->area !== 'admin') {
            // Falls für Query ein Alias existiert, zu diesem weiterleiten
            if ($this->routerAliases->uriAliasExists($this->request->query) === true &&
                $this->request->originalQuery !== $this->routerAliases->getUriAlias($this->request->query) . '/'
            ) {
                $this->redirect->permanent($this->request->query); // URI-Alias wird von Router::route() erzeugt
            }
        }

        return;
    }

} 