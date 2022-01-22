<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Http;

use ACP3\Core\Http\Request\UserAgent;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\ServerBag;

abstract class AbstractRequest implements RequestInterface
{
    protected string $homepage = '';
    /**
     * @var \ACP3\Core\Http\Request\UserAgent
     */
    protected ?UserAgent $userAgent = null;

    public function __construct(private RequestStack $requestStack)
    {
        $this->fillParameterBags();
    }

    public function getSymfonyRequest(): SymfonyRequest
    {
        return $this->requestStack->getCurrentRequest();
    }

    /**
     * {@inheritdoc}
     */
    public function getScheme(): string
    {
        return $this->getSymfonyRequest()->getScheme();
    }

    /**
     * {@inheritdoc}
     */
    public function getHost(): string
    {
        return $this->getSymfonyRequest()->getHost();
    }

    /**
     * {@inheritdoc}
     */
    public function getHttpHost(): string
    {
        return $this->getSymfonyRequest()->getHttpHost();
    }

    /**
     * {@inheritdoc}
     */
    public function isXmlHttpRequest(): bool
    {
        return $this->getSymfonyRequest()->isXmlHttpRequest();
    }

    /**
     * {@inheritdoc}
     */
    public function getCookies(): ParameterBag
    {
        return $this->getSymfonyRequest()->cookies;
    }

    /**
     * {@inheritdoc}
     */
    public function getFiles(): FileBag
    {
        return $this->getSymfonyRequest()->files;
    }

    /**
     * {@inheritdoc}
     */
    public function getPost(): ParameterBag
    {
        return $this->getSymfonyRequest()->request;
    }

    /**
     * {@inheritdoc}
     */
    public function getServer(): ServerBag
    {
        return $this->getSymfonyRequest()->server;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserAgent(): UserAgent
    {
        return $this->userAgent;
    }

    /**
     * {@inheritdoc}
     */
    public function setHomepage(string $homepage): RequestInterface
    {
        $this->homepage = $homepage;

        return $this;
    }

    protected function fillParameterBags(): void
    {
        $this->userAgent = new UserAgent($this->getSymfonyRequest()->server);
    }
}
