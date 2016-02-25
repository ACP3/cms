<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Controller\Context;

use ACP3\Core\ACL;
use ACP3\Core\Config;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Modules;
use ACP3\Core\Router;
use ACP3\Core\RouterInterface;
use ACP3\Core\User;
use ACP3\Core\Validation\Validator;
use ACP3\Core\View;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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
     * @var \ACP3\Core\ACL
     */
    protected $acl;
    /**
     * @var \ACP3\Core\User
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
     * @var \ACP3\Core\RouterInterface
     */
    protected $router;
    /**
     * @var \ACP3\Core\Validation\Validator
     */
    protected $validator;
    /**
     * @var \ACP3\Core\View
     */
    protected $view;
    /**
     * @var \ACP3\Core\Config
     */
    protected $config;
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;

    /**
     * WidgetContext constructor.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface   $container
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param \ACP3\Core\ACL                                              $acl
     * @param \ACP3\Core\User                                             $user
     * @param \ACP3\Core\I18n\Translator                                  $translator
     * @param \ACP3\Core\Modules                                          $modules
     * @param \ACP3\Core\Http\RequestInterface                            $request
     * @param \ACP3\Core\RouterInterface                                  $router
     * @param \ACP3\Core\Validation\Validator                             $validator
     * @param \ACP3\Core\View                                             $view
     * @param \ACP3\Core\Config                                           $config
     * @param \ACP3\Core\Environment\ApplicationPath                      $appPath
     */
    public function __construct(
        ContainerInterface $container,
        EventDispatcherInterface $eventDispatcher,
        ACL $acl,
        User $user,
        Translator $translator,
        Modules $modules,
        RequestInterface $request,
        RouterInterface $router,
        Validator $validator,
        View $view,
        Config $config,
        ApplicationPath $appPath
    ) {
        $this->container = $container;
        $this->eventDispatcher = $eventDispatcher;
        $this->acl = $acl;
        $this->user = $user;
        $this->translator = $translator;
        $this->modules = $modules;
        $this->request = $request;
        $this->router = $router;
        $this->validator = $validator;
        $this->view = $view;
        $this->config = $config;
        $this->appPath = $appPath;
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
     * @return \ACP3\Core\ACL
     */
    public function getACL()
    {
        return $this->acl;
    }

    /**
     * @return \ACP3\Core\User
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
     * @return \ACP3\Core\RouterInterface
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @return \ACP3\Core\Validation\Validator
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * @return \ACP3\Core\View
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @return \ACP3\Core\Config
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
}
