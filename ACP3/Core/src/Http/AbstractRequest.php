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
    protected ?UserAgent $userAgent = null;

    public function __construct(private readonly RequestStack $requestStack)
    {
        $this->fillParameterBags();
    }

    public function getSymfonyRequest(): SymfonyRequest
    {
        return $this->requestStack->getCurrentRequest();
    }

    public function getScheme(): string
    {
        return $this->getSymfonyRequest()->getScheme();
    }

    public function getHost(): string
    {
        return $this->getSymfonyRequest()->getHost();
    }

    public function getHttpHost(): string
    {
        return $this->getSymfonyRequest()->getHttpHost();
    }

    public function isXmlHttpRequest(): bool
    {
        return $this->getSymfonyRequest()->isXmlHttpRequest();
    }

    public function getCookies(): ParameterBag
    {
        return $this->getSymfonyRequest()->cookies;
    }

    public function getFiles(): FileBag
    {
        return $this->getSymfonyRequest()->files;
    }

    public function getPost(): ParameterBag
    {
        return $this->getSymfonyRequest()->request;
    }

    public function getServer(): ServerBag
    {
        return $this->getSymfonyRequest()->server;
    }

    public function getUserAgent(): UserAgent
    {
        return $this->userAgent;
    }

    public function setHomepage(string $homepage): RequestInterface
    {
        $this->homepage = $homepage;

        return $this;
    }

    private function fillParameterBags(): void
    {
        $this->userAgent = new UserAgent($this->getSymfonyRequest()->server);
    }
}
