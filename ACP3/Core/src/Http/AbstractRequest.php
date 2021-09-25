<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Http;

use ACP3\Core\Http\Request\UserAgent;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\RequestStack;

abstract class AbstractRequest implements RequestInterface
{
    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * @var string
     */
    protected $homepage = '';
    /**
     * @var \ACP3\Core\Http\Request\UserAgent
     */
    protected $userAgent;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;

        $this->fillParameterBags();
    }

    public function getSymfonyRequest(): SymfonyRequest
    {
        return $this->requestStack->getCurrentRequest();
    }

    /**
     * {@inheritdoc}
     */
    public function getScheme()
    {
        return $this->getSymfonyRequest()->getScheme();
    }

    /**
     * {@inheritdoc}
     */
    public function getHost()
    {
        return $this->getSymfonyRequest()->getHost();
    }

    /**
     * {@inheritdoc}
     */
    public function getHttpHost()
    {
        return $this->getSymfonyRequest()->getHttpHost();
    }

    /**
     * {@inheritdoc}
     */
    public function isXmlHttpRequest()
    {
        return $this->getSymfonyRequest()->isXmlHttpRequest();
    }

    /**
     * {@inheritdoc}
     */
    public function getCookies()
    {
        return $this->getSymfonyRequest()->cookies;
    }

    /**
     * {@inheritdoc}
     */
    public function getFiles()
    {
        return $this->getSymfonyRequest()->files;
    }

    /**
     * {@inheritdoc}
     */
    public function getPost()
    {
        return $this->getSymfonyRequest()->request;
    }

    /**
     * {@inheritdoc}
     */
    public function getServer()
    {
        return $this->getSymfonyRequest()->server;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * {@inheritdoc}
     */
    public function setHomepage($homepage)
    {
        $this->homepage = $homepage;

        return $this;
    }

    protected function fillParameterBags()
    {
        $this->userAgent = new UserAgent($this->getSymfonyRequest()->server);
    }
}
