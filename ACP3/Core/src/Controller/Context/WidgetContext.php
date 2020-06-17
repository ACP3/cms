<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Controller\Context;

use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

class WidgetContext
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\View
     */
    private $view;
    /**
     * @var SettingsInterface
     */
    private $config;
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    private $appPath;
    /**
     * @var \Symfony\Component\HttpFoundation\Response
     */
    private $response;

    public function __construct(
        ContainerInterface $container,
        Translator $translator,
        RequestInterface $request,
        View $view,
        SettingsInterface $config,
        ApplicationPath $appPath,
        Response $response
    ) {
        $this->container = $container;
        $this->translator = $translator;
        $this->request = $request;
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
     * @return \ACP3\Core\I18n\Translator
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * @return \ACP3\Core\Http\RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
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
