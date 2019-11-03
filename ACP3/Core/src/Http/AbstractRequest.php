<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Http;

use ACP3\Core\Http\Request\UserAgent;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

abstract class AbstractRequest implements RequestInterface
{
    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $symfonyRequest;

    /**
     * @var string
     */
    protected $homepage = '';
    /**
     * @var \ACP3\Core\Http\Request\UserAgent
     */
    protected $userAgent;

    /**
     * AbstractRequest constructor.
     */
    public function __construct(SymfonyRequest $symfonyRequest)
    {
        $this->symfonyRequest = $symfonyRequest;

        $this->fillParameterBags();
    }

    /**
     * @return SymfonyRequest
     */
    public function getSymfonyRequest()
    {
        return $this->symfonyRequest;
    }

    /**
     * {@inheritdoc}
     */
    public function getScheme()
    {
        return $this->symfonyRequest->getScheme();
    }

    /**
     * {@inheritdoc}
     */
    public function getHost()
    {
        return $this->symfonyRequest->getHost();
    }

    /**
     * {@inheritdoc}
     */
    public function getHttpHost()
    {
        return $this->symfonyRequest->getHttpHost();
    }

    /**
     * {@inheritdoc}
     */
    public function isXmlHttpRequest()
    {
        return $this->symfonyRequest->isXmlHttpRequest();
    }

    /**
     * {@inheritdoc}
     */
    public function getCookies()
    {
        return $this->symfonyRequest->cookies;
    }

    /**
     * {@inheritdoc}
     */
    public function getFiles()
    {
        return $this->symfonyRequest->files;
    }

    /**
     * {@inheritdoc}
     */
    public function getPost()
    {
        return $this->symfonyRequest->request;
    }

    /**
     * {@inheritdoc}
     */
    public function getServer()
    {
        return $this->symfonyRequest->server;
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
        $this->userAgent = new UserAgent($this->symfonyRequest->server);
    }
}
