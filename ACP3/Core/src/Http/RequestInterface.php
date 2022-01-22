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
use Symfony\Component\HttpFoundation\ServerBag;

interface RequestInterface
{
    public function getSymfonyRequest(): SymfonyRequest;

    /**
     * Returns the used protocol of the current request.
     */
    public function getScheme(): string;

    /**
     * Returns the hostname of the current request.
     */
    public function getHost(): string;

    /**
     * Returns the HTTP host being requested.
     *
     * The port name will be appended to the host if it's non-standard.
     */
    public function getHttpHost(): string;

    public function getQuery(): string;

    /**
     * Returns the original requested query.
     */
    public function getPathInfo(): string;

    public function getArea(): string;

    public function getModule(): string;

    public function getController(): string;

    public function getAction(): string;

    /**
     * Returns the currently requested module, controller and controller action.
     */
    public function getFullPath(): string;

    /**
     * Returns the currently requested module, controller and controller action without the area prefix.
     */
    public function getFullPathWithoutArea(): string;

    /**
     * Returns the currently requested module and controller.
     */
    public function getModuleAndController(): string;

    /**
     * Returns the currently requested module and controller without the area prefix.
     */
    public function getModuleAndControllerWithoutArea(): string;

    public function isHomepage(): bool;

    /**
     * Gibt die URI-Parameter aus.
     */
    public function getParameters(): ParameterBag;

    /**
     * Gibt die bereinigte URI-Query aus, d.h. ohne die anzuzeigende Seite.
     */
    public function getUriWithoutPages(): string;

    public function isXmlHttpRequest(): bool;

    /**
     * Returns the parameter bag of the $_COOKIE superglobal.
     */
    public function getCookies(): ParameterBag;

    /**
     * Returns the parameter bag of the uploaded files ($_FILES superglobal).
     */
    public function getFiles(): FileBag;

    /**
     * Returns the parameter bag of the $_POST superglobal.
     */
    public function getPost(): ParameterBag;

    /**
     * Returns the parameter bag of the $_SERVER superglobal.
     */
    public function getServer(): ServerBag;

    public function getUserAgent(): UserAgent;

    /**
     * Processes the request.
     */
    public function processQuery(): void;

    /**
     * @return static
     */
    public function setHomepage(string $homepage): self;

    public function setPathInfo(?string $pathInfo = null): void;
}
