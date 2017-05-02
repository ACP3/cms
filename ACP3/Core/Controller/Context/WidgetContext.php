<?php
/**
 * Copyright (c) by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Controller\Context;

use ACP3\Core\ACL;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Helpers\ResultsPerPage;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Modules;
use ACP3\Core\Router\RouterInterface;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\Validation\Validator;
use ACP3\Core\View;
use ACP3\Modules\ACP3\Users\Model\UserModel;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class WidgetContext
 * @package ACP3\Core\Controller\Context
 */
class WidgetContext
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $eventDispatcher;
    /**
     * @var \ACP3\Modules\ACP3\Users\Model\UserModel
     */
    protected $user;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    protected $translator;
    /**
     * @var \ACP3\Core\Modules
     */
    protected $modules;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;
    /**
     * @var \ACP3\Core\Router\RouterInterface
     */
    protected $router;
    /**
     * @var \ACP3\Core\View
     */
    protected $view;
    /**
     * @var SettingsInterface
     */
    protected $config;
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;
    /**
     * @var \Symfony\Component\HttpFoundation\Response
     */
    private $response;

    /**
     * WidgetContext constructor.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param \ACP3\Modules\ACP3\Users\Model\UserModel $user
     * @param \ACP3\Core\I18n\Translator $translator
     * @param \ACP3\Core\Modules $modules
     * @param \ACP3\Core\Http\RequestInterface $request
     * @param \ACP3\Core\Router\RouterInterface $router
     * @param \ACP3\Core\View $view
     * @param \ACP3\Core\Settings\SettingsInterface $config
     * @param \ACP3\Core\Environment\ApplicationPath $appPath
     * @param Response $response
     */
    public function __construct(
        ContainerInterface $container,
        EventDispatcherInterface $eventDispatcher,
        UserModel $user,
        Translator $translator,
        Modules $modules,
        RequestInterface $request,
        RouterInterface $router,
        View $view,
        SettingsInterface $config,
        ApplicationPath $appPath,
        Response $response
    ) {
        $this->container = $container;
        $this->eventDispatcher = $eventDispatcher;
        $this->user = $user;
        $this->translator = $translator;
        $this->modules = $modules;
        $this->request = $request;
        $this->router = $router;
        $this->view = $view;
        $this->config = $config;
        $this->appPath = $appPath;
        $this->response = $response;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    /**
     * @return \ACP3\Modules\ACP3\Users\Model\UserModel
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return \ACP3\Core\I18n\Translator
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * @return \ACP3\Core\Modules
     */
    public function getModules()
    {
        return $this->modules;
    }

    /**
     * @return \ACP3\Core\Http\RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return \ACP3\Core\Router\RouterInterface
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @return \ACP3\Core\View
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @return \ACP3\Core\Settings\SettingsInterface
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return \ACP3\Core\Environment\ApplicationPath
     */
    public function getAppPath()
    {
        return $this->appPath;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}
