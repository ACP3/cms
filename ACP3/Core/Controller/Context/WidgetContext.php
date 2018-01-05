<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Controller\Context;

use ACP3\Core\Controller\ActionResultFactory;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\LocaleInterface;
use ACP3\Core\I18n\TranslatorInterface;
use ACP3\Core\Modules\Modules;
use ACP3\Core\Router\RouterInterface;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View;
use ACP3\Modules\ACP3\Users\Model\UserModel;
use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;

class WidgetContext
{
    /**
     * @var ContainerInterface
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
     * @var \ACP3\Core\I18n\TranslatorInterface
     */
    protected $translator;
    /**
     * @var \ACP3\Core\Modules\Modules
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
     * @var ActionResultFactory
     */
    private $actionResultFactory;
    /**
     * @var LocaleInterface
     */
    private $locale;

    /**
     * WidgetContext constructor.
     * @param ContainerInterface $container
     * @param EventDispatcherInterface $eventDispatcher
     * @param UserModel $user
     * @param TranslatorInterface $translator
     * @param LocaleInterface $locale
     * @param Modules $modules
     * @param RequestInterface $request
     * @param RouterInterface $router
     * @param View $view
     * @param SettingsInterface $config
     * @param ApplicationPath $appPath
     * @param Response $response
     * @param ActionResultFactory $actionResultFactory
     */
    public function __construct(
        ContainerInterface $container,
        EventDispatcherInterface $eventDispatcher,
        UserModel $user,
        TranslatorInterface $translator,
        LocaleInterface $locale,
        Modules $modules,
        RequestInterface $request,
        RouterInterface $router,
        View $view,
        SettingsInterface $config,
        ApplicationPath $appPath,
        Response $response,
        ActionResultFactory $actionResultFactory
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
        $this->actionResultFactory = $actionResultFactory;
        $this->locale = $locale;
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
     * @return \ACP3\Core\I18n\TranslatorInterface
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * @return \ACP3\Core\Modules\Modules
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

    /**
     * @return ActionResultFactory
     */
    public function getActionResultFactory(): ActionResultFactory
    {
        return $this->actionResultFactory;
    }

    /**
     * @return LocaleInterface
     */
    public function getLocale(): LocaleInterface
    {
        return $this->locale;
    }
}
